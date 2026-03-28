<?php

namespace App\Http\Controllers;

use App\Models\BirdBatch;
use App\Models\BirdSale;
use App\Models\CropInputExpense;
use App\Models\CropSale;
use App\Models\EggProduction;
use App\Models\EggSale;
use App\Models\Income;
use App\Models\Payroll;
use App\Models\Planting;
use App\Models\PoultryExpense;
use App\Models\Task;
use App\Services\PriorityBankIntegrationService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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
        $eggStockByBatch = $this->eggStockByBatch($eggsPerCrate);
        $totalRemainingBirds = (int) BirdBatch::query()
            ->withSum('dailyRecords', 'mortality_count')
            ->withSum('dailyRecords', 'cull_count')
            ->withSum('birdSales', 'quantity_sold')
            ->get()
            ->sum(fn (BirdBatch $batch) => $batch->remaining_birds);

        $totalIncome = Schema::hasTable('incomes')
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

        $activityFinancialSummary = $this->financialActivitySummary(7);
        $recentActivities = $this->buildRecentActivity(15);

        return view('dashboard', compact(
            'chart',
            'eggsProducedToday',
            'eggsSoldToday',
            'totalEggsInStock',
            'eggsInStockCrates',
            'eggsInStockLoose',
            'eggsPerCrate',
            'eggStockByBatch',
            'totalRemainingBirds',
            'incomeExpenditureBalance',
            'bankBalance',
            'activityFinancialSummary',
            'recentActivities'
        ));
    }

    /**
     * Income and expense totals for the last N days (for Recent Activity summary).
     */
    protected function financialActivitySummary(int $days): array
    {
        $from = Carbon::now()->subDays($days - 1)->startOfDay();
        $to = Carbon::now()->endOfDay();

        $income = $this->sumAllIncomeBetween($from, $to);
        $expense = (float) PoultryExpense::whereBetween('date', [$from, $to])->sum('amount');
        if (Schema::hasTable('crop_input_expenses')) {
            $expense += (float) CropInputExpense::whereBetween('date', [$from, $to])->sum('amount');
        }

        return [
            'days' => $days,
            'income' => $income,
            'expenses' => $expense,
            'net' => $income - $expense,
        ];
    }

    protected function sumAllIncomeBetween(Carbon $from, Carbon $to): float
    {
        $total = 0.0;
        if (Schema::hasTable('incomes')) {
            $total += (float) Income::whereBetween('received_on', [$from->toDateString(), $to->toDateString()])->sum('amount');
        }
        $total += (float) EggSale::whereBetween('date', [$from, $to])->get()
            ->sum(fn (EggSale $s) => (float) $s->quantity_sold * (float) $s->price_per_unit);
        $total += (float) BirdSale::whereBetween('date', [$from, $to])->get()
            ->sum(fn (BirdSale $s) => (float) $s->quantity_sold * (float) $s->price_per_bird);
        $total += (float) CropSale::whereBetween('date', [$from, $to])->get()
            ->sum(fn (CropSale $s) => (float) $s->quantity_sold * (float) $s->price_per_unit);

        return $total;
    }

    /**
     * Mixed recent activity: financial records, payroll, tasks, plantings, crop inputs (no bird-batch spam).
     *
     * @return Collection<int, array{sort_at: \Carbon\Carbon, title: string, subtitle: string, badge: string, badge_class: string, icon: string, icon_style: string}>
     */
    protected function buildRecentActivity(int $limit): Collection
    {
        $items = collect();

        foreach (PoultryExpense::with('expenseCategory', 'farm')->latest()->take(6)->get() as $e) {
            $cat = $e->expenseCategory?->name ?? ($e->getRawOriginal('category') ?: 'Expense');
            $items->push([
                'sort_at' => $e->created_at ?? Carbon::parse($e->date),
                'title' => 'Poultry expense',
                'subtitle' => $cat . ' · ₵' . number_format((float) $e->amount, 2) . ($e->description ? ' — ' . Str::limit($e->description, 55) : '') . ($e->farm ? ' · ' . $e->farm->name : ''),
                'badge' => 'Expense',
                'badge_class' => 'danger',
                'icon' => 'fa-arrow-up',
                'icon_style' => 'linear-gradient(135deg, rgba(244, 67, 54, 0.12), rgba(239, 83, 80, 0.12))',
            ]);
        }

        if (Schema::hasTable('incomes')) {
            foreach (Income::query()->orderByDesc('received_on')->take(5)->get() as $inc) {
                $items->push([
                    'sort_at' => Carbon::parse($inc->received_on)->endOfDay(),
                    'title' => 'Income recorded',
                    'subtitle' => ($inc->category ?? 'Income') . ' · ₵' . number_format((float) $inc->amount, 2) . ($inc->description ? ' — ' . Str::limit($inc->description, 55) : ''),
                    'badge' => 'Income',
                    'badge_class' => 'success',
                    'icon' => 'fa-arrow-down',
                    'icon_style' => 'linear-gradient(135deg, rgba(46, 125, 50, 0.12), rgba(139, 195, 74, 0.12))',
                ]);
            }
        }

        foreach (EggSale::with('birdBatch.farm')->latest()->take(4)->get() as $sale) {
            $amt = (float) $sale->quantity_sold * (float) $sale->price_per_unit;
            $items->push([
                'sort_at' => $sale->created_at ?? Carbon::parse($sale->date),
                'title' => 'Egg sale',
                'subtitle' => '₵' . number_format($amt, 2) . ' · ' . $sale->quantity_sold . ' ' . $sale->unit_type . ($sale->buyer_name ? ' · ' . $sale->buyer_name : ''),
                'badge' => 'Sales',
                'badge_class' => 'success',
                'icon' => 'fa-egg',
                'icon_style' => 'linear-gradient(135deg, rgba(46, 125, 50, 0.12), rgba(139, 195, 74, 0.12))',
            ]);
        }

        foreach (BirdSale::with('birdBatch')->latest()->take(3)->get() as $sale) {
            $amt = (float) $sale->quantity_sold * (float) $sale->price_per_bird;
            $items->push([
                'sort_at' => $sale->created_at ?? Carbon::parse($sale->date),
                'title' => 'Bird sale',
                'subtitle' => '₵' . number_format($amt, 2) . ' · ' . $sale->quantity_sold . ' birds' . ($sale->buyer_name ? ' · ' . $sale->buyer_name : ''),
                'badge' => 'Sales',
                'badge_class' => 'success',
                'icon' => 'fa-dove',
                'icon_style' => 'linear-gradient(135deg, rgba(46, 125, 50, 0.12), rgba(139, 195, 74, 0.12))',
            ]);
        }

        foreach (CropSale::latest()->take(3)->get() as $sale) {
            $amt = (float) $sale->quantity_sold * (float) $sale->price_per_unit;
            $items->push([
                'sort_at' => $sale->created_at ?? Carbon::parse($sale->date),
                'title' => 'Crop sale',
                'subtitle' => '₵' . number_format($amt, 2) . ' · ' . $sale->quantity_sold . ' units' . ($sale->buyer_name ? ' · ' . $sale->buyer_name : ''),
                'badge' => 'Sales',
                'badge_class' => 'success',
                'icon' => 'fa-coins',
                'icon_style' => 'linear-gradient(135deg, rgba(255, 152, 0, 0.12), rgba(255, 193, 7, 0.12))',
            ]);
        }

        if (Schema::hasTable('crop_input_expenses')) {
            foreach (CropInputExpense::latest()->take(4)->get() as $ce) {
                $items->push([
                    'sort_at' => $ce->created_at ?? Carbon::parse($ce->date),
                    'title' => 'Crop input expense',
                    'subtitle' => ($ce->category ?? 'Expense') . ' · ₵' . number_format((float) $ce->amount, 2) . ($ce->description ? ' — ' . Str::limit($ce->description, 50) : ''),
                    'badge' => 'Expense',
                    'badge_class' => 'danger',
                    'icon' => 'fa-leaf',
                    'icon_style' => 'linear-gradient(135deg, rgba(255, 152, 0, 0.12), rgba(255, 193, 7, 0.12))',
                ]);
            }
        }

        if (Schema::hasTable('payrolls')) {
            foreach (Payroll::with('employee')->where('status', 'paid')->whereNotNull('paid_at')->orderByDesc('paid_at')->take(4)->get() as $pr) {
                $name = $pr->employee?->full_name ?? 'Employee';
                $period = Carbon::parse($pr->pay_period)->format('M Y');
                $items->push([
                    'sort_at' => Carbon::parse($pr->paid_at),
                    'title' => 'Salary paid',
                    'subtitle' => $name . ' · ₵' . number_format((float) $pr->net_pay, 2) . ' · ' . $period,
                    'badge' => 'Payroll',
                    'badge_class' => 'primary',
                    'icon' => 'fa-money-bill-wave',
                    'icon_style' => 'linear-gradient(135deg, rgba(14, 165, 233, 0.12), rgba(56, 189, 248, 0.12))',
                ]);
            }
        }

        foreach (Task::query()->latest('updated_at')->take(5)->get() as $task) {
            $items->push([
                'sort_at' => $task->updated_at ?? $task->created_at,
                'title' => 'Task: ' . Str::limit($task->title, 45),
                'subtitle' => ucfirst($task->status ?? 'open') . ($task->due_date ? ' · due ' . Carbon::parse($task->due_date)->format('M j') : ''),
                'badge' => 'Task',
                'badge_class' => $task->status === 'pending' ? 'warning' : 'secondary',
                'icon' => 'fa-tasks',
                'icon_style' => 'linear-gradient(135deg, rgba(103, 58, 183, 0.12), rgba(156, 39, 176, 0.12))',
            ]);
        }

        foreach (Planting::latest()->take(3)->get() as $planting) {
            $items->push([
                'sort_at' => $planting->created_at,
                'title' => 'Planting',
                'subtitle' => ($planting->crop_name ?? 'Crop') . ' · ' . ucfirst($planting->status ?? 'active'),
                'badge' => 'Crop',
                'badge_class' => 'warning',
                'icon' => 'fa-seedling',
                'icon_style' => 'linear-gradient(135deg, rgba(255, 152, 0, 0.12), rgba(255, 193, 7, 0.12))',
            ]);
        }

        return $items->sortByDesc(fn (array $row) => $row['sort_at']->timestamp)->values()->take($limit);
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

    protected function eggStockByBatch(int $eggsPerCrate): \Illuminate\Support\Collection
    {
        return BirdBatch::query()
            ->whereIn('purpose', ['egg_production', 'layer'])
            ->with('farm')
            ->withSum('eggProductions', 'eggs_collected')
            ->withSum('eggProductions', 'cracked_or_damaged')
            ->withSum('eggProductions', 'eggs_used_internal')
            ->with('eggSales:id,bird_batch_id,unit_type,quantity_sold')
            ->get()
            ->map(function (BirdBatch $batch) use ($eggsPerCrate) {
                $produced = (int) ($batch->egg_productions_sum_eggs_collected ?? 0);
                $cracked = (int) ($batch->egg_productions_sum_cracked_or_damaged ?? 0);
                $internal = (int) ($batch->egg_productions_sum_eggs_used_internal ?? 0);
                $netProduced = max(0, $produced - $cracked - $internal);

                $soldPieces = (int) $batch->eggSales->where('unit_type', 'piece')->sum('quantity_sold');
                $soldCrates = (int) $batch->eggSales->whereIn('unit_type', ['crate', 'tray'])->sum('quantity_sold');
                $totalSoldPieces = $soldPieces + ($soldCrates * $eggsPerCrate);

                $eggsInStock = max(0, $netProduced - $totalSoldPieces);

                return [
                    'batch_code' => $batch->batch_code,
                    'farm_name' => $batch->farm->name ?? 'N/A',
                    'eggs_in_stock' => $eggsInStock,
                    'crates' => intdiv($eggsInStock, $eggsPerCrate),
                    'loose_eggs' => $eggsInStock % $eggsPerCrate,
                    'has_activity' => $netProduced > 0 || $totalSoldPieces > 0,
                ];
            })
            ->filter(fn (array $row) => $row['has_activity'] || $row['eggs_in_stock'] > 0)
            ->sortByDesc('eggs_in_stock')
            ->values();
    }
}
