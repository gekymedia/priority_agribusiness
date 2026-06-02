<?php

namespace App\Services;

use App\Models\EggClientSale;
use App\Models\EggSale;
use Illuminate\Support\Facades\DB;

class EggClientSaleRecorder
{
    /**
     * @param  array{
     *     bird_batch_id: int,
     *     date: string,
     *     buyer_name?: ?string,
     *     buyer_contact?: ?string,
     *     amount_paid?: ?float,
     *     notes?: ?string,
     *     items: array<int, array{
     *         egg_size: string,
     *         quantity: int,
     *         price_per_unit: float,
     *         payment_status: string,
     *         line_notes?: ?string
     *     }>
     * }  $data
     */
    public function record(array $data): EggClientSale
    {
        return DB::transaction(function () use ($data) {
            $clientSale = EggClientSale::create([
                'bird_batch_id' => $data['bird_batch_id'],
                'date' => $data['date'],
                'buyer_name' => $data['buyer_name'] ?? null,
                'buyer_contact' => $data['buyer_contact'] ?? null,
                'amount_paid' => 0,
                'notes' => $data['notes'] ?? null,
            ]);

            $computedPaid = 0.0;
            foreach ($data['items'] as $item) {
                $lineTotal = round($item['quantity'] * $item['price_per_unit'], 2);
                if ($item['payment_status'] === EggSale::PAYMENT_PAID) {
                    $computedPaid += $lineTotal;
                }

                EggSale::create([
                    'egg_client_sale_id' => $clientSale->id,
                    'bird_batch_id' => $data['bird_batch_id'],
                    'date' => $data['date'],
                    'quantity_sold' => $item['quantity'],
                    'unit_type' => 'piece',
                    'egg_size' => $item['egg_size'],
                    'price_per_unit' => $item['price_per_unit'],
                    'payment_status' => $item['payment_status'],
                    'buyer_name' => $data['buyer_name'] ?? null,
                    'buyer_contact' => $data['buyer_contact'] ?? null,
                    'notes' => $item['line_notes'] ?? null,
                ]);
            }

            $amountPaid = array_key_exists('amount_paid', $data) && $data['amount_paid'] !== null
                ? (float) $data['amount_paid']
                : $computedPaid;

            $clientSale->update(['amount_paid' => $amountPaid]);

            return $clientSale->fresh(['items', 'birdBatch.farm']);
        });
    }
}
