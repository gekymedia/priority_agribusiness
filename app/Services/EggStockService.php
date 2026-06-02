<?php

namespace App\Services;

use App\Models\EggProduction;
use App\Models\EggSale;
use App\Models\PaymentSetting;
use Illuminate\Support\Facades\Schema;

class EggStockService
{
    public function eggsPerCrate(): int
    {
        $eggsPerCrate = 30;
        if (class_exists(PaymentSetting::class) && Schema::hasTable('payment_settings')) {
            $eggsPerCrate = max(1, (int) PaymentSetting::getEggMarketEggsPerCrate());
        }

        return $eggsPerCrate;
    }

    public function totalEggsInStock(?int $eggsPerCrate = null): int
    {
        $eggsPerCrate ??= $this->eggsPerCrate();

        $totalProducedNet = (int) EggProduction::query()->get()
            ->sum(fn ($p) => max(0, $p->eggs_collected - $p->cracked_or_damaged - $p->eggs_used_internal));

        $eggSales = EggSale::query()->get();
        $soldPieces = (int) $eggSales->where('unit_type', 'piece')->sum('quantity_sold');
        $soldCrates = (int) $eggSales->whereIn('unit_type', ['crate', 'tray'])->sum('quantity_sold');
        $totalSoldPieces = $soldPieces + ($soldCrates * $eggsPerCrate);

        return max(0, $totalProducedNet - $totalSoldPieces);
    }

    /**
     * @return array{total: int, crates: int, loose: int, eggs_per_crate: int}
     */
    public function summary(): array
    {
        $eggsPerCrate = $this->eggsPerCrate();
        $total = $this->totalEggsInStock($eggsPerCrate);

        return [
            'total' => $total,
            'crates' => intdiv($total, $eggsPerCrate),
            'loose' => $total % $eggsPerCrate,
            'eggs_per_crate' => $eggsPerCrate,
        ];
    }
}
