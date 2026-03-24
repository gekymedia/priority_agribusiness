<?php

namespace App\Http\Controllers;

use App\Models\EggProduction;
use App\Models\EggSale;
use App\Models\Income;
use App\Models\BirdBatch;
use App\Models\PoultryExpense;
use App\Services\PriorityBankIntegrationService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $chart = $this->buildEggProductionVsSalesChart(31);
        $eggsProducedToday = $this->eggsProducedToday();
        $eggsSoldToday = $this->eggsSoldToday();
        $eggsPerCrate = $this->eggsPerCrate();
        $totalEggsInStock = $this->totalEggsInStock($eggsPerCrate);
        $eggsInStockCrates = intdiv($totalEggsInStock, $eggsPerCrate);
        $eggsInStockLoose = $totalEggsInStock % $eggsPerCrate;
        $totalRemainingBirds = (int) BirdBatch::query()
            ->withSum('dailyRecords', 'mortality_count')
            ->withSum('dailyRecords', 'cull_count')
            ->withSum('birdSales', 'quantity_sold')
            ->get()
            ->sum(fn (BirdBatch $batch) => $batch->remaining_birds);

        $totalIncome = \Illuminate\Support\Facades\Schema::hasTable('incomes')
            ? (float) Income::sum('amount')
            : 0.0;
        $totalExpenditure = (float) PoultryExpense::sum('amount');
        $incomeExpenditureBalance = $totalIncome - $totalExpenditure;

        $bankBalance = null;
        try {
            $client = PriorityBankIntegrationService::clientFromSettings();
            $result = $client->getBalance();
            if ($result && isset($result['balance'])) {
                $bankBalance = (float) $result['balance'];
            }
        } catch (\Throwable $e) {
            // Leave bankBalance null on failure
        }

        return view('dashboard', compact(
            'chart',
            'eggsProducedToday',
            'eggsSoldToday',
            'totalEggsInStock',
            'eggsInStockCrates',
            'eggsInStockLoose',
            'eggsPerCrate',
            'totalRemainingBirds',
            'incomeExpenditureBalance',
            'bankBalance'
        ));
    }

    protected function eggsProducedToday(): int
    {
        $rows = EggProduction::whereDate('date', today())->get();
        return (int) $rows->sum(fn ($p) => max(0, $p->eggs_collected - $p->cracked_or_damaged - $p->eggs_used_internal));
    }

    protected function eggsSoldToday(): int
    {
        $eggsPerCrate = $this->eggsPerCrate();
        $sold = EggSale::whereDate('date', today())->get();
        $pieces = $sold->where('unit_type', 'piece')->sum('quantity_sold');
        $crates = $sold->whereIn('unit_type', ['crate', 'tray'])->sum('quantity_sold');
        return (int) ($pieces + ($crates * $eggsPerCrate));
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

        $eggsPerCrate = $this->eggsPerCrate();

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
            $salesCrates = (clone $salesQuery)->whereIn('unit_type', ['crate', 'tray'])->sum('quantity_sold');
            $sales[] = (int) ($salesPieces + $salesCrates * $eggsPerCrate);
        }

        return [
            'labels' => $labels,
            'production' => $production,
            'sales' => $sales,
        ];
    }

    protected function eggsPerCrate(): int
    {
        $eggsPerCrate = 30;
        if (class_exists(\App\Models\PaymentSetting::class) && \Illuminate\Support\Facades\Schema::hasTable('payment_settings')) {
            $eggsPerCrate = max(1, (int) \App\Models\PaymentSetting::getEggMarketEggsPerCrate());
        }

        return $eggsPerCrate;
    }

    protected function totalEggsInStock(int $eggsPerCrate): int
    {
        $totalProducedNet = (int) EggProduction::query()->get()
            ->sum(fn ($p) => max(0, $p->eggs_collected - $p->cracked_or_damaged - $p->eggs_used_internal));

        $eggSales = EggSale::query()->get();
        $soldPieces = (int) $eggSales->where('unit_type', 'piece')->sum('quantity_sold');
        $soldCrates = (int) $eggSales->whereIn('unit_type', ['crate', 'tray'])->sum('quantity_sold');
        $totalSoldPieces = $soldPieces + ($soldCrates * $eggsPerCrate);

        return max(0, $totalProducedNet - $totalSoldPieces);
    }
}
