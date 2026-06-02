<?php

namespace App\Services;

use App\Models\EggClientSale;
use App\Models\EggSale;
use App\Models\BirdSale;
use App\Models\CropSale;
use App\Models\PoultryExpense;
use App\Models\CropInputExpense;
use App\Models\Income;
use App\Models\SystemSetting;
use App\Services\PriorityBankApiClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PriorityBankIntegrationService
{
    protected PriorityBankApiClient $client;
    protected string $systemId;

    public function __construct()
    {
        $baseUrl = rtrim((string) (SystemSetting::get('priority_bank_api_url') ?: config('services.priority_bank.api_url', '')), '/');
        $token = SystemSetting::get('priority_bank_api_token') ?: config('services.priority_bank.api_token');
        $this->client = new PriorityBankApiClient($baseUrl, $token);
        $this->systemId = (string) (SystemSetting::get('priority_bank_system_id') ?: config('services.priority_bank.system_id', 'priority_agriculture'));
    }

    /**
     * Create a client using SystemSetting/config (e.g. for getBalance from Dashboard).
     */
    public static function clientFromSettings(): PriorityBankApiClient
    {
        $baseUrl = rtrim((string) (SystemSetting::get('priority_bank_api_url') ?: config('services.priority_bank.api_url', '')), '/');
        $token = SystemSetting::get('priority_bank_api_token') ?: config('services.priority_bank.api_token');
        return new PriorityBankApiClient($baseUrl, $token);
    }

    /**
     * Push egg sale income to Priority Bank
     */
    public function pushEggSale(EggSale $eggSale): bool
    {
        if (! SystemSetting::get('priority_bank_api_token') && ! config('services.priority_bank.api_token')) {
            return false;
        }

        try {
            $totalAmount = $eggSale->quantity_sold * $eggSale->price_per_unit;
            $extId = 'agri_egg_sale_' . $eggSale->id;

            $result = $this->client->pushIncome(
                systemId: $this->systemId,
                externalTransactionId: $extId,
                amount: (float) $totalAmount,
                date: $eggSale->date->format('Y-m-d'),
                channel: 'cash',
                options: [
                    'notes' => "Egg sale: {$eggSale->quantity_sold} {$eggSale->unit_type} @ {$eggSale->price_per_unit} per unit" . 
                               ($eggSale->buyer_name ? " - Buyer: {$eggSale->buyer_name}" : '') .
                               ($eggSale->notes ? " - {$eggSale->notes}" : ''),
                    'income_category_name' => 'Egg Sales',
                    'metadata' => [
                        'operation' => 'poultry_farm',
                        'quantity' => $eggSale->quantity_sold,
                        'unit_type' => $eggSale->unit_type,
                        'price_per_unit' => $eggSale->price_per_unit,
                        'buyer_name' => $eggSale->buyer_name,
                    ],
                ]
            );

            if ($this->succeeded($result, $eggSale, $extId)) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Exception pushing egg sale to Priority Bank', [
                'egg_sale_id' => $eggSale->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Push manual income record to Priority Bank.
     */
    public function pushManualIncome(Income $income): bool
    {
        if (! SystemSetting::get('priority_bank_api_token') && ! config('services.priority_bank.api_token')) {
            return false;
        }

        try {
            if (empty($income->external_transaction_id)) {
                $income->external_transaction_id = str_pad((string) random_int(0, 9999999999), 10, '0', STR_PAD_LEFT);
            }

            $extId = $income->external_transaction_id;

            $result = $this->client->pushIncome(
                systemId: $this->systemId,
                externalTransactionId: $extId,
                amount: (float) $income->amount,
                date: $income->received_on?->format('Y-m-d') ?? now()->format('Y-m-d'),
                channel: 'bank',
                options: [
                    'notes' => $income->description ?? $income->category,
                    'income_category_name' => $income->category,
                ]
            );

            if ($this->succeeded($result, $income, $extId)) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Exception pushing manual income to Priority Bank', [
                'income_id' => $income->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    protected function markSynced(Model $model, string $externalTransactionId): void
    {
        $updates = [];

        if (Schema::hasColumn($model->getTable(), 'external_transaction_id')) {
            $updates['external_transaction_id'] = $externalTransactionId;
        }

        if (Schema::hasColumn($model->getTable(), 'priority_bank_synced_at')) {
            $updates['priority_bank_synced_at'] = now();
        }

        if ($updates !== []) {
            $model->forceFill($updates)->save();
        }
    }

    /**
     * Push client egg sale income to Priority Bank (paid amount only).
     */
    public function pushEggClientSale(EggClientSale $clientSale): bool
    {
        if (! SystemSetting::get('priority_bank_api_token') && ! config('services.priority_bank.api_token')) {
            return false;
        }

        $clientSale->loadMissing('items');
        $amountPaid = (float) $clientSale->amount_paid;

        if ($amountPaid <= 0) {
            return false;
        }

        try {
            $sizeSummary = $clientSale->items
                ->groupBy('egg_size')
                ->map(fn ($group, $size) => $group->sum('quantity_sold') . ' ' . $size)
                ->implode(', ');

            $extId = 'agri_egg_client_sale_' . $clientSale->id;

            $result = $this->client->pushIncome(
                systemId: $this->systemId,
                externalTransactionId: $extId,
                amount: $amountPaid,
                date: $clientSale->date->format('Y-m-d'),
                channel: 'cash',
                options: [
                    'notes' => 'Egg client sale' .
                               ($sizeSummary ? ": {$sizeSummary}" : '') .
                               ($clientSale->buyer_name ? " - Buyer: {$clientSale->buyer_name}" : '') .
                               ($clientSale->notes ? " - {$clientSale->notes}" : ''),
                    'income_category_name' => 'Egg Sales',
                    'metadata' => [
                        'operation' => 'poultry_farm',
                        'buyer_name' => $clientSale->buyer_name,
                        'total_amount' => $clientSale->total_amount,
                        'amount_paid' => $amountPaid,
                        'balance' => $clientSale->balance,
                    ],
                ]
            );

            if ($this->succeeded($result, $clientSale, $extId)) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Exception pushing egg client sale to Priority Bank', [
                'egg_client_sale_id' => $clientSale->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Push bird sale income to Priority Bank
     */
    public function pushBirdSale(BirdSale $birdSale): bool
    {
        if (! SystemSetting::get('priority_bank_api_token') && ! config('services.priority_bank.api_token')) {
            return false;
        }

        try {
            $totalAmount = $birdSale->quantity_sold * $birdSale->price_per_bird;
            $extId = 'agri_bird_sale_' . $birdSale->id;

            $result = $this->client->pushIncome(
                systemId: $this->systemId,
                externalTransactionId: $extId,
                amount: (float) $totalAmount,
                date: $birdSale->date->format('Y-m-d'),
                channel: 'cash',
                options: [
                    'notes' => "Bird sale: {$birdSale->quantity_sold} birds @ {$birdSale->price_per_bird} per bird" .
                               ($birdSale->buyer_name ? " - Buyer: {$birdSale->buyer_name}" : '') .
                               ($birdSale->notes ? " - {$birdSale->notes}" : ''),
                    'income_category_name' => 'Bird Sales',
                    'metadata' => [
                        'operation' => 'poultry_farm',
                        'quantity' => $birdSale->quantity_sold,
                        'price_per_bird' => $birdSale->price_per_bird,
                        'buyer_name' => $birdSale->buyer_name,
                    ],
                ]
            );

            if ($this->succeeded($result, $birdSale, $extId)) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Exception pushing bird sale to Priority Bank', [
                'bird_sale_id' => $birdSale->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Push crop sale income to Priority Bank
     */
    public function pushCropSale(CropSale $cropSale): bool
    {
        if (! SystemSetting::get('priority_bank_api_token') && ! config('services.priority_bank.api_token')) {
            return false;
        }

        try {
            $totalAmount = $cropSale->quantity_sold * $cropSale->price_per_unit;
            $extId = 'agri_crop_sale_' . $cropSale->id;

            $result = $this->client->pushIncome(
                systemId: $this->systemId,
                externalTransactionId: $extId,
                amount: (float) $totalAmount,
                date: $cropSale->date->format('Y-m-d'),
                channel: 'cash',
                options: [
                    'notes' => "Crop sale: {$cropSale->quantity_sold} units @ {$cropSale->price_per_unit} per unit" .
                               ($cropSale->buyer_name ? " - Buyer: {$cropSale->buyer_name}" : '') .
                               ($cropSale->notes ? " - {$cropSale->notes}" : ''),
                    'income_category_name' => 'Crop Sales',
                    'metadata' => [
                        'operation' => 'crop_farm',
                        'quantity' => $cropSale->quantity_sold,
                        'price_per_unit' => $cropSale->price_per_unit,
                    ],
                ]
            );

            if ($this->succeeded($result, $cropSale, $extId)) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Exception pushing crop sale to Priority Bank', [
                'crop_sale_id' => $cropSale->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Push poultry expense to Priority Bank
     */
    public function pushPoultryExpense(PoultryExpense $expense): bool
    {
        if (! SystemSetting::get('priority_bank_api_token') && ! config('services.priority_bank.api_token')) {
            return false;
        }

        try {
            $extId = $expense->external_transaction_id ?: ('agri_poultry_expense_' . $expense->id);
            $legacyCategory = $expense->getRawOriginal('category');
            $categoryName = $expense->expenseCategory?->name ?? ($legacyCategory ?: null);
            $result = $this->client->pushExpense(
                systemId: $this->systemId,
                externalTransactionId: $extId,
                amount: (float) $expense->amount,
                date: $expense->date->format('Y-m-d'),
                channel: 'cash',
                options: [
                    'notes' => $expense->description ?? 'Poultry expense',
                    'expense_category_name' => $this->mapExpenseCategory($categoryName),
                    'metadata' => [
                        'operation' => 'poultry_farm',
                        'category' => $categoryName,
                        'farm_id' => $expense->farm_id,
                    ],
                ]
            );

            if ($this->succeeded($result, $expense, $extId)) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Exception pushing poultry expense to Priority Bank', [
                'expense_id' => $expense->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Push crop input expense to Priority Bank
     */
    public function pushCropInputExpense(CropInputExpense $expense): bool
    {
        if (! SystemSetting::get('priority_bank_api_token') && ! config('services.priority_bank.api_token')) {
            return false;
        }

        try {
            $extId = 'agri_crop_expense_' . $expense->id;

            $result = $this->client->pushExpense(
                systemId: $this->systemId,
                externalTransactionId: $extId,
                amount: (float) $expense->amount,
                date: $expense->date->format('Y-m-d'),
                channel: 'cash',
                options: [
                    'notes' => $expense->description ?? 'Crop input expense',
                    'expense_category_name' => $this->mapExpenseCategory($expense->category),
                    'metadata' => [
                        'operation' => 'crop_farm',
                        'category' => $expense->category,
                    ],
                ]
            );

            if ($this->succeeded($result, $expense, $extId)) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Exception pushing crop expense to Priority Bank', [
                'expense_id' => $expense->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * @param  array<string, mixed>|null  $result
     */
    protected function succeeded(?array $result, Model $model, string $externalTransactionId): bool
    {
        if ($result && ($result['success'] ?? false)) {
            $this->markSynced($model, $externalTransactionId);

            return true;
        }

        return false;
    }

    /**
     * Map expense category
     */
    protected function mapExpenseCategory(?string $category): string
    {
        $mapping = [
            'Feed' => 'Feed',
            'Vet Services' => 'Veterinary Services',
            'Labor' => 'Labor',
            'Medication' => 'Medication',
            'Vaccination' => 'Veterinary Services',
            'Equipment' => 'Equipment',
            'Seeds' => 'Seeds',
            'Fertilizer' => 'Fertilizer',
            'Pesticides' => 'Pesticides',
        ];

        return $mapping[$category] ?? 'Other Expenses';
    }
}

