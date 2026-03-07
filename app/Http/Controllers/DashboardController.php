<?php

namespace App\Http\Controllers;

use App\Models\EggProduction;
use App\Models\EggSale;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $chart = $this->buildEggProductionVsSalesChart(31);
        $eggsProducedToday = $this->eggsProducedToday();
        $eggsSoldToday = $this->eggsSoldToday();

        return view('dashboard', compact('chart', 'eggsProducedToday', 'eggsSoldToday'));
    }

    protected function eggsProducedToday(): int
    {
        $rows = EggProduction::whereDate('date', today())->get();
        return (int) $rows->sum(fn ($p) => max(0, $p->eggs_collected - $p->cracked_or_damaged - $p->eggs_used_internal));
    }

    protected function eggsSoldToday(): int
    {
        $eggsPerCrate = 30;
        if (class_exists(\App\Models\PaymentSetting::class) && \Illuminate\Support\Facades\Schema::hasTable('payment_settings')) {
            $eggsPerCrate = max(1, (int) \App\Models\PaymentSetting::getEggMarketEggsPerCrate());
        }
        $sold = EggSale::whereDate('date', today())->get();
        $pieces = $sold->where('unit_type', 'piece')->sum('quantity_sold');
        $crates = $sold->where('unit_type', 'crate')->sum('quantity_sold');
        return (int) ($pieces + $crates * $eggsPerCrate);
    }

    /**
     * Build daily data for egg production vs egg sales (last N days).
     * Returns ['labels' => [...], 'production' => [...], 'sales' => [...]]
     */
    protected function buildEggProductionVsSalesChart(int $days): array
    {
        $end = Carbon::today();
        $start = Carbon::today()->subDays($days - 1);
        $labels = [];
        $production = [];
        $sales = [];

        $eggsPerCrate = 30;
        if (class_exists(\App\Models\PaymentSetting::class) && \Illuminate\Support\Facades\Schema::hasTable('payment_settings')) {
            $eggsPerCrate = max(1, (int) \App\Models\PaymentSetting::getEggMarketEggsPerCrate());
        }

        for ($d = 0; $d < $days; $d++) {
            $date = $start->copy()->addDays($d);
            $labels[] = $date->format('M j');
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();

            $prodTotal = EggProduction::whereBetween('date', [$dayStart, $dayEnd])->sum('eggs_collected');
            $prodCracked = EggProduction::whereBetween('date', [$dayStart, $dayEnd])->sum('cracked_or_damaged');
            $prodInternal = EggProduction::whereBetween('date', [$dayStart, $dayEnd])->sum('eggs_used_internal');
            $production[] = (int) max(0, $prodTotal - $prodCracked - $prodInternal);

            $salesQuery = EggSale::whereBetween('date', [$dayStart, $dayEnd]);
            $salesPieces = (clone $salesQuery)->where('unit_type', 'piece')->sum('quantity_sold');
            $salesCrates = (clone $salesQuery)->where('unit_type', 'crate')->sum('quantity_sold');
            $sales[] = (int) ($salesPieces + $salesCrates * $eggsPerCrate);
        }

        return [
            'labels' => $labels,
            'production' => $production,
            'sales' => $sales,
        ];
    }
}
