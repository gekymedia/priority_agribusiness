<?php

namespace App\Services;

use App\Models\EggSale;
use App\Models\BirdSale;
use App\Models\CropSale;
use App\Models\PoultryExpense;
use App\Models\CropInputExpense;
use App\Services\PriorityBankApiClient;
use Illuminate\Support\Facades\Log;

class PriorityBankIntegrationService
{
    protected PriorityBankApiClient $client;
    protected string $systemId = 'priority_agriculture';

    public function __construct()
    {
        $this->client = new PriorityBankApiClient(
            config('services.priority_bank.api_url'),
            config('services.priority_bank.api_token')
        );
    }

    /**
     * Push egg sale income to Priority Bank
     */
    public function pushEggSale(EggSale $eggSale): bool
    {
        if (!config('services.priority_bank.api_token')) {
            return false;
        }

        try {
            $totalAmount = $eggSale->quantity_sold * $eggSale->price_per_unit;

            $result = $this->client->pushIncome(
                systemId: $this->systemId,
                externalTransactionId: 'agri_egg_sale_' . $eggSale->id,
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

            return $result && $result['success'];
        } catch (\Exception $e) {
            Log::error('Exception pushing egg sale to Priority Bank', [
                'egg_sale_id' => $eggSale->id,
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
        if (!config('services.priority_bank.api_token')) {
            return false;
        }

        try {
            $totalAmount = $birdSale->quantity_sold * $birdSale->price_per_bird;

            $result = $this->client->pushIncome(
                systemId: $this->systemId,
                externalTransactionId: 'agri_bird_sale_' . $birdSale->id,
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

            return $result && $result['success'];
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
        if (!config('services.priority_bank.api_token')) {
            return false;
        }

        try {
            $totalAmount = $cropSale->quantity_sold * $cropSale->price_per_unit;

            $result = $this->client->pushIncome(
                systemId: $this->systemId,
                externalTransactionId: 'agri_crop_sale_' . $cropSale->id,
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

            return $result && $result['success'];
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
        if (!config('services.priority_bank.api_token')) {
            return false;
        }

        try {
            $result = $this->client->pushExpense(
                systemId: $this->systemId,
                externalTransactionId: 'agri_poultry_expense_' . $expense->id,
                amount: (float) $expense->amount,
                date: $expense->date->format('Y-m-d'),
                channel: 'cash',
                options: [
                    'notes' => $expense->description ?? 'Poultry expense',
                    'expense_category_name' => $this->mapExpenseCategory($expense->category?->name ?? $expense->category),
                    'metadata' => [
                        'operation' => 'poultry_farm',
                        'category' => $expense->category?->name ?? $expense->category,
                        'farm_id' => $expense->farm_id,
                    ],
                ]
            );

            return $result && $result['success'];
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
        if (!config('services.priority_bank.api_token')) {
            return false;
        }

        try {
            $result = $this->client->pushExpense(
                systemId: $this->systemId,
                externalTransactionId: 'agri_crop_expense_' . $expense->id,
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

            return $result && $result['success'];
        } catch (\Exception $e) {
            Log::error('Exception pushing crop expense to Priority Bank', [
                'expense_id' => $expense->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
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

