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
use Illuminate\Support\Facades\Log;

class PriorityBankSyncService
{
    public function __construct(
        protected PriorityBankIntegrationService $integration
    ) {}

    public function isConfigured(): bool
    {
        $baseUrl = rtrim((string) (SystemSetting::get('priority_bank_api_url') ?: config('services.priority_bank.api_url', '')), '/');
        $token = SystemSetting::get('priority_bank_api_token') ?: config('services.priority_bank.api_token');

        return $baseUrl !== '' && ! empty($token);
    }

    /**
     * @return array{total: int, income: int, expense: int}
     */
    public function countPending(?string $from = null, ?string $to = null): array
    {
        $income = $this->pendingIncomesQuery($from, $to)->count()
            + $this->pendingEggClientSalesQuery($from, $to)->count()
            + $this->pendingLegacyEggSalesQuery($from, $to)->count()
            + $this->pendingBirdSalesQuery($from, $to)->count()
            + $this->pendingCropSalesQuery($from, $to)->count();

        $expense = $this->pendingPoultryExpensesQuery($from, $to)->count()
            + $this->pendingCropInputExpensesQuery($from, $to)->count();

        return [
            'total' => $income + $expense,
            'income' => $income,
            'expense' => $expense,
        ];
    }

    /**
     * @return array{synced: int, failed: int, errors: array<int, string>}
     */
    public function syncAll(?string $from = null, ?string $to = null): array
    {
        $results = ['synced' => 0, 'failed' => 0, 'errors' => []];

        if (! $this->isConfigured()) {
            return array_merge($results, ['errors' => ['Priority Bank is not configured. Set API URL and token in Settings.']]);
        }

        foreach ($this->pendingIncomesQuery($from, $to)->get() as $income) {
            $this->attemptSync('Income #' . $income->id, fn () => $this->syncIncome($income), $results);
        }

        foreach ($this->pendingEggClientSalesQuery($from, $to)->with('items')->get() as $sale) {
            $this->attemptSync('Egg client sale #' . $sale->id, fn () => $this->integration->pushEggClientSale($sale), $results);
        }

        foreach ($this->pendingLegacyEggSalesQuery($from, $to)->get() as $sale) {
            $this->attemptSync('Egg sale #' . $sale->id, fn () => $this->integration->pushEggSale($sale), $results);
        }

        foreach ($this->pendingBirdSalesQuery($from, $to)->get() as $sale) {
            $this->attemptSync('Bird sale #' . $sale->id, fn () => $this->integration->pushBirdSale($sale), $results);
        }

        foreach ($this->pendingCropSalesQuery($from, $to)->get() as $sale) {
            $this->attemptSync('Crop sale #' . $sale->id, fn () => $this->integration->pushCropSale($sale), $results);
        }

        foreach ($this->pendingPoultryExpensesQuery($from, $to)->with('expenseCategory')->get() as $expense) {
            $this->attemptSync('Expense #' . $expense->id, fn () => $this->integration->pushPoultryExpense($expense), $results);
        }

        foreach ($this->pendingCropInputExpensesQuery($from, $to)->get() as $expense) {
            $this->attemptSync('Crop expense #' . $expense->id, fn () => $this->integration->pushCropInputExpense($expense), $results);
        }

        return $results;
    }

    public function syncIncome(Income $income): bool
    {
        return $this->integration->pushManualIncome($income);
    }

    public function syncPoultryExpense(PoultryExpense $expense): bool
    {
        return $this->integration->pushPoultryExpense($expense);
    }

    protected function attemptSync(string $label, callable $callback, array &$results): void
    {
        try {
            $ok = (bool) $callback();
            if ($ok) {
                $results['synced']++;
            } else {
                $results['failed']++;
                $results['errors'][] = $label . ': sync rejected by bank API';
            }
        } catch (\Throwable $e) {
            $results['failed']++;
            $results['errors'][] = $label . ': ' . $e->getMessage();
            Log::error('Priority Bank bulk sync item failed', ['label' => $label, 'error' => $e->getMessage()]);
        }
    }

    protected function pendingIncomesQuery(?string $from, ?string $to)
    {
        return Income::query()
            ->where(fn ($q) => $q->whereNull('external_transaction_id')->orWhere('external_transaction_id', ''))
            ->when($from, fn ($q) => $q->whereDate('received_on', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('received_on', '<=', $to));
    }

    protected function pendingPoultryExpensesQuery(?string $from, ?string $to)
    {
        return PoultryExpense::query()
            ->where(fn ($q) => $q->whereNull('external_transaction_id')->orWhere('external_transaction_id', ''))
            ->when($from, fn ($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('date', '<=', $to));
    }

    protected function pendingCropInputExpensesQuery(?string $from, ?string $to)
    {
        return CropInputExpense::query()
            ->where(fn ($q) => $q->whereNull('external_transaction_id')->orWhere('external_transaction_id', ''))
            ->when($from, fn ($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('date', '<=', $to));
    }

    protected function pendingEggClientSalesQuery(?string $from, ?string $to)
    {
        return EggClientSale::query()
            ->where(fn ($q) => $q->whereNull('external_transaction_id')->orWhere('external_transaction_id', ''))
            ->where('amount_paid', '>', 0)
            ->when($from, fn ($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('date', '<=', $to));
    }

    protected function pendingLegacyEggSalesQuery(?string $from, ?string $to)
    {
        return EggSale::query()
            ->whereNull('egg_client_sale_id')
            ->where(fn ($q) => $q->whereNull('external_transaction_id')->orWhere('external_transaction_id', ''))
            ->when($from, fn ($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('date', '<=', $to));
    }

    protected function pendingBirdSalesQuery(?string $from, ?string $to)
    {
        return BirdSale::query()
            ->where(fn ($q) => $q->whereNull('external_transaction_id')->orWhere('external_transaction_id', ''))
            ->when($from, fn ($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('date', '<=', $to));
    }

    protected function pendingCropSalesQuery(?string $from, ?string $to)
    {
        return CropSale::query()
            ->where(fn ($q) => $q->whereNull('external_transaction_id')->orWhere('external_transaction_id', ''))
            ->when($from, fn ($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('date', '<=', $to));
    }
}
