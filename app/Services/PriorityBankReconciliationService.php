<?php

namespace App\Services;

use App\Models\BirdSale;
use App\Models\CropInputExpense;
use App\Models\CropSale;
use App\Models\EggClientSale;
use App\Models\EggSale;
use App\Models\Income;
use App\Models\PoultryExpense;
use App\Models\SystemSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Finds local finance records that were pushed to Priority Bank before
 * external_transaction_id was stored locally, and backfills sync status.
 */
class PriorityBankReconciliationService
{
    public function __construct(
        protected PriorityBankSyncService $syncService,
    ) {}

    protected function client(): PriorityBankApiClient
    {
        return PriorityBankIntegrationService::clientFromSettings();
    }

    public function isConfigured(): bool
    {
        return $this->syncService->isConfigured();
    }

    protected function systemId(): string
    {
        return (string) (SystemSetting::get('priority_bank_system_id') ?: config('services.priority_bank.system_id', 'priority_agriculture'));
    }

    /**
     * @return array{
     *     bank_api_available: bool,
     *     matched_in_bank: int,
     *     backfilled: int,
     *     still_pending: int,
     *     bank_only: int,
     *     details: array<int, string>,
     *     errors: array<int, string>
     * }
     */
    public function reconcile(?string $from = null, ?string $to = null): array
    {
        $result = [
            'bank_api_available' => false,
            'matched_in_bank' => 0,
            'backfilled' => 0,
            'still_pending' => 0,
            'bank_only' => 0,
            'details' => [],
            'errors' => [],
        ];

        if (! $this->isConfigured()) {
            $result['errors'][] = 'Priority Bank is not configured.';

            return $result;
        }

        $pending = $this->collectPendingRecords($from, $to);
        $bankTransactions = $this->client()->getTransactions($this->systemId(), $from, $to);
        $result['bank_api_available'] = $bankTransactions !== null;

        if ($bankTransactions === null) {
            return $this->reconcileViaDeterministicBackfill($pending, $result);
        }

        $usedBankKeys = [];

        foreach ($pending as $item) {
            $match = $this->findBankMatch($item, $bankTransactions, $usedBankKeys);

            if ($match === null) {
                $result['still_pending']++;
                continue;
            }

            $result['matched_in_bank']++;
            $usedBankKeys[$match['key']] = true;

            if ($this->backfillExternalId($item['model'], $item['expected_external_id'])) {
                $result['backfilled']++;
                $result['details'][] = $item['label'] . ' ↔ bank: ' . Str::limit($match['description'] ?? 'matched', 60);
            }
        }

        foreach ($bankTransactions as $index => $tx) {
            $key = $this->bankTransactionKey($tx, $index);
            if (! isset($usedBankKeys[$key])) {
                $result['bank_only']++;
            }
        }

        return $result;
    }

    /**
     * When the bank transactions API is unavailable, backfill deterministic external IDs
     * for auto-pushed record types (egg sales, poultry/crop expenses, etc.).
     * These IDs were always sent on create; missing locally means sync predates ID storage.
     *
     * @param  Collection<int, array<string, mixed>>  $pending
     * @param  array<string, mixed>  $result
     * @return array<string, mixed>
     */
    protected function reconcileViaDeterministicBackfill(Collection $pending, array $result): array
    {
        foreach ($pending as $item) {
            if (! ($item['auto_pushed'] ?? false)) {
                $result['still_pending']++;
                continue;
            }

            if ($this->backfillExternalId($item['model'], $item['expected_external_id'])) {
                $result['backfilled']++;
                $result['matched_in_bank']++;
                $result['details'][] = $item['label'] . ' → ' . $item['expected_external_id'];
            }
        }

        if ($result['backfilled'] > 0) {
            $result['details'][] = 'Bank transaction list API unavailable; restored expected external IDs for auto-pushed records.';
        }

        return $result;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function collectPendingRecords(?string $from = null, ?string $to = null): Collection
    {
        $items = collect();

        Income::query()
            ->where(fn ($q) => $q->whereNull('external_transaction_id')->orWhere('external_transaction_id', ''))
            ->when($from, fn ($q) => $q->whereDate('received_on', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('received_on', '<=', $to))
            ->each(function (Income $income) use ($items) {
                $items->push($this->pendingItem(
                    model: $income,
                    label: 'Manual income #' . $income->id,
                    date: $income->received_on?->format('Y-m-d'),
                    amount: (float) $income->amount,
                    type: 'income',
                    description: $income->description ?? $income->category,
                    expectedExternalId: $income->external_transaction_id ?: null,
                    autoPushed: false,
                ));
            });

        EggClientSale::query()
            ->where(fn ($q) => $q->whereNull('external_transaction_id')->orWhere('external_transaction_id', ''))
            ->where('amount_paid', '>', 0)
            ->when($from, fn ($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('date', '<=', $to))
            ->with('items')
            ->each(function (EggClientSale $sale) use ($items) {
                $items->push($this->pendingItem(
                    model: $sale,
                    label: 'Egg client sale #' . $sale->id,
                    date: $sale->date->format('Y-m-d'),
                    amount: (float) $sale->amount_paid,
                    type: 'income',
                    description: $this->eggClientSaleBankNotes($sale),
                    expectedExternalId: 'agri_egg_client_sale_' . $sale->id,
                    autoPushed: true,
                ));
            });

        EggSale::query()
            ->whereNull('egg_client_sale_id')
            ->where(fn ($q) => $q->whereNull('external_transaction_id')->orWhere('external_transaction_id', ''))
            ->when($from, fn ($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('date', '<=', $to))
            ->each(function (EggSale $sale) use ($items) {
                $items->push($this->pendingItem(
                    model: $sale,
                    label: 'Egg sale #' . $sale->id,
                    date: $sale->date->format('Y-m-d'),
                    amount: (float) ($sale->quantity_sold * $sale->price_per_unit),
                    type: 'income',
                    description: "Egg sale: {$sale->quantity_sold} {$sale->unit_type} @ {$sale->price_per_unit} per unit" .
                        ($sale->buyer_name ? " - Buyer: {$sale->buyer_name}" : ''),
                    expectedExternalId: 'agri_egg_sale_' . $sale->id,
                    autoPushed: true,
                ));
            });

        BirdSale::query()
            ->where(fn ($q) => $q->whereNull('external_transaction_id')->orWhere('external_transaction_id', ''))
            ->when($from, fn ($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('date', '<=', $to))
            ->each(function (BirdSale $sale) use ($items) {
                $total = (float) ($sale->quantity_sold * $sale->price_per_bird);
                $items->push($this->pendingItem(
                    model: $sale,
                    label: 'Bird sale #' . $sale->id,
                    date: $sale->date->format('Y-m-d'),
                    amount: $total,
                    type: 'income',
                    description: "Bird sale: {$sale->quantity_sold} birds @ {$sale->price_per_bird} per bird" .
                        ($sale->buyer_name ? " - Buyer: {$sale->buyer_name}" : ''),
                    expectedExternalId: 'agri_bird_sale_' . $sale->id,
                    autoPushed: true,
                ));
            });

        CropSale::query()
            ->where(fn ($q) => $q->whereNull('external_transaction_id')->orWhere('external_transaction_id', ''))
            ->when($from, fn ($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('date', '<=', $to))
            ->each(function (CropSale $sale) use ($items) {
                $total = (float) ($sale->quantity_sold * $sale->price_per_unit);
                $items->push($this->pendingItem(
                    model: $sale,
                    label: 'Crop sale #' . $sale->id,
                    date: $sale->date->format('Y-m-d'),
                    amount: $total,
                    type: 'income',
                    description: "Crop sale: {$sale->quantity_sold} units @ {$sale->price_per_unit} per unit" .
                        ($sale->buyer_name ? " - Buyer: {$sale->buyer_name}" : ''),
                    expectedExternalId: 'agri_crop_sale_' . $sale->id,
                    autoPushed: true,
                ));
            });

        PoultryExpense::query()
            ->where(fn ($q) => $q->whereNull('external_transaction_id')->orWhere('external_transaction_id', ''))
            ->when($from, fn ($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('date', '<=', $to))
            ->each(function (PoultryExpense $expense) use ($items) {
                if ($this->isPayrollOrWebhookExpense($expense)) {
                    return;
                }

                $items->push($this->pendingItem(
                    model: $expense,
                    label: 'Poultry expense #' . $expense->id,
                    date: $expense->date->format('Y-m-d'),
                    amount: (float) $expense->amount,
                    type: 'expense',
                    description: $expense->description ?? 'Poultry expense',
                    expectedExternalId: 'agri_poultry_expense_' . $expense->id,
                    autoPushed: true,
                ));
            });

        CropInputExpense::query()
            ->where(fn ($q) => $q->whereNull('external_transaction_id')->orWhere('external_transaction_id', ''))
            ->when($from, fn ($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('date', '<=', $to))
            ->each(function (CropInputExpense $expense) use ($items) {
                $items->push($this->pendingItem(
                    model: $expense,
                    label: 'Crop expense #' . $expense->id,
                    date: $expense->date->format('Y-m-d'),
                    amount: (float) $expense->amount,
                    type: 'expense',
                    description: $expense->description ?? 'Crop input expense',
                    expectedExternalId: 'agri_crop_expense_' . $expense->id,
                    autoPushed: true,
                ));
            });

        return $items;
    }

    protected function pendingItem(
        Model $model,
        string $label,
        ?string $date,
        float $amount,
        string $type,
        string $description,
        ?string $expectedExternalId,
        bool $autoPushed,
    ): array {
        return [
            'model' => $model,
            'label' => $label,
            'date' => $date,
            'amount' => round($amount, 2),
            'type' => $type,
            'description' => $description,
            'expected_external_id' => $expectedExternalId,
            'auto_pushed' => $autoPushed,
            'normalized_description' => $this->normalizeText($description),
        ];
    }

    protected function isPayrollOrWebhookExpense(PoultryExpense $expense): bool
    {
        $description = (string) ($expense->description ?? '');

        return str_contains($description, '[PB_ID:')
            || str_starts_with((string) ($expense->external_transaction_id ?? ''), 'agri_payroll_');
    }

    protected function eggClientSaleBankNotes(EggClientSale $clientSale): string
    {
        $sizeSummary = $clientSale->items
            ->groupBy('egg_size')
            ->map(fn ($group, $size) => $group->sum('quantity_sold') . ' ' . $size)
            ->implode(', ');

        return 'Egg client sale' .
            ($sizeSummary ? ": {$sizeSummary}" : '') .
            ($clientSale->buyer_name ? " - Buyer: {$clientSale->buyer_name}" : '');
    }

    /**
     * @param  array<string, mixed>  $item
     * @param  array<int, array<string, mixed>>  $bankTransactions
     * @param  array<string, bool>  $usedBankKeys
     * @return array<string, mixed>|null
     */
    protected function findBankMatch(array $item, array $bankTransactions, array $usedBankKeys): ?array
    {
        $candidates = [];

        foreach ($bankTransactions as $index => $tx) {
            $key = $this->bankTransactionKey($tx, $index);
            if (isset($usedBankKeys[$key])) {
                continue;
            }

            $bankExtId = (string) ($tx['external_transaction_id'] ?? '');
            if ($bankExtId !== '' && $bankExtId === $item['expected_external_id']) {
                return array_merge($tx, ['key' => $key]);
            }

            if (! $this->bankTransactionMatchesItem($item, $tx)) {
                continue;
            }

            $score = $this->descriptionSimilarity(
                $item['normalized_description'],
                $this->normalizeText((string) ($tx['notes'] ?? $tx['description'] ?? ''))
            );

            $candidates[] = ['tx' => $tx, 'key' => $key, 'score' => $score];
        }

        if ($candidates === []) {
            return null;
        }

        usort($candidates, fn ($a, $b) => $b['score'] <=> $a['score']);
        $best = $candidates[0];

        if ($best['score'] < 0.45) {
            return null;
        }

        if (count($candidates) > 1 && ($candidates[1]['score'] ?? 0) >= ($best['score'] - 0.05)) {
            return null;
        }

        return array_merge($best['tx'], ['key' => $best['key'], 'description' => $best['tx']['notes'] ?? $best['tx']['description'] ?? '']);
    }

    /**
     * @param  array<string, mixed>  $item
     * @param  array<string, mixed>  $tx
     */
    protected function bankTransactionMatchesItem(array $item, array $tx): bool
    {
        $txDate = substr((string) ($tx['date'] ?? $tx['transaction_date'] ?? ''), 0, 10);
        if ($txDate !== $item['date']) {
            return false;
        }

        $txAmount = round(abs((float) ($tx['amount'] ?? 0)), 2);
        if ($txAmount !== $item['amount']) {
            return false;
        }

        $txType = strtolower((string) ($tx['type'] ?? $tx['transaction_type'] ?? ''));
        if ($txType !== '') {
            $isCredit = in_array($txType, ['credit', 'income', 'deposit'], true);
            $isDebit = in_array($txType, ['debit', 'expense', 'withdrawal'], true);

            if ($item['type'] === 'income' && $isDebit) {
                return false;
            }
            if ($item['type'] === 'expense' && $isCredit) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $tx
     */
    protected function bankTransactionKey(array $tx, int $index): string
    {
        if (! empty($tx['id'])) {
            return 'id:' . $tx['id'];
        }

        if (! empty($tx['external_transaction_id'])) {
            return 'ext:' . $tx['external_transaction_id'];
        }

        return 'idx:' . $index . ':' . ($tx['date'] ?? '') . ':' . ($tx['amount'] ?? '');
    }

    protected function normalizeText(string $text): string
    {
        $text = Str::lower($text);
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text) ?? $text;
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;

        return trim($text);
    }

    protected function descriptionSimilarity(string $a, string $b): float
    {
        if ($a === '' || $b === '') {
            return 0.0;
        }

        if ($a === $b) {
            return 1.0;
        }

        if (str_contains($a, $b) || str_contains($b, $a)) {
            return 0.85;
        }

        similar_text($a, $b, $percent);

        return $percent / 100;
    }

    protected function backfillExternalId(Model $model, ?string $externalId): bool
    {
        if ($externalId === null || $externalId === '') {
            return false;
        }

        $updates = [];

        if (Schema::hasColumn($model->getTable(), 'external_transaction_id')) {
            $updates['external_transaction_id'] = $externalId;
        }

        if (Schema::hasColumn($model->getTable(), 'priority_bank_synced_at')) {
            $updates['priority_bank_synced_at'] = now();
        }

        if ($updates === []) {
            return false;
        }

        $model->forceFill($updates)->save();

        return true;
    }
}
