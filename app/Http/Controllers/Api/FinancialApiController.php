<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialApiController extends Controller
{
    /**
     * Get financial summary
     */
    public function summary(Request $request)
    {
        $user = $request->user();
        $year = $request->get('year', now()->year);

        // Total sales (eggs + birds + crops)
        $eggSales = $user->eggSales()
            ->whereYear('date', $year)
            ->sum(DB::raw('quantity_sold * price_per_unit'));

        $birdSales = $user->birdSales()
            ->whereYear('date', $year)
            ->sum(DB::raw('quantity_sold * price_per_unit'));

        // Assuming crop sales exist (you may need to adjust based on your models)
        $cropSales = 0; // Placeholder

        $totalSales = $eggSales + $birdSales + $cropSales;

        // Total expenses
        $totalExpenses = $user->poultryExpenses()
            ->whereYear('date', $year)
            ->sum('amount');

        $cropExpenses = $user->cropInputExpenses()
            ->whereYear('date', $year)
            ->sum('amount');

        $totalExpenses += $cropExpenses;

        // Profit/Loss
        $profitLoss = $totalSales - $totalExpenses;

        return response()->json([
            'success' => true,
            'data' => [
                'year' => $year,
                'total_sales' => $totalSales,
                'total_expenses' => $totalExpenses,
                'profit_loss' => $profitLoss,
                'breakdown' => [
                    'egg_sales' => $eggSales,
                    'bird_sales' => $birdSales,
                    'crop_sales' => $cropSales,
                    'poultry_expenses' => $user->poultryExpenses()->whereYear('date', $year)->sum('amount'),
                    'crop_expenses' => $cropExpenses,
                ]
            ]
        ]);
    }

    /**
     * Get sales data
     */
    public function sales(Request $request)
    {
        $user = $request->user();
        $startDate = $request->get('start_date', now()->startOfYear());
        $endDate = $request->get('end_date', now()->endOfYear());

        $eggSales = $user->eggSales()
            ->whereBetween('date', [$startDate, $endDate])
            ->select('date', 'quantity_sold', 'price_per_unit', 'unit_type', DB::raw('(quantity_sold * price_per_unit) as total_amount'))
            ->get();

        $birdSales = $user->birdSales()
            ->whereBetween('date', [$startDate, $endDate])
            ->select('date', 'quantity_sold', 'price_per_unit', 'unit_type', DB::raw('(quantity_sold * price_per_unit) as total_amount'))
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                'egg_sales' => $eggSales,
                'bird_sales' => $birdSales,
                'summary' => [
                    'total_egg_sales' => $eggSales->sum('total_amount'),
                    'total_bird_sales' => $birdSales->sum('total_amount'),
                    'total_sales' => $eggSales->sum('total_amount') + $birdSales->sum('total_amount'),
                ]
            ]
        ]);
    }

    /**
     * Get expenses data
     */
    public function expenses(Request $request)
    {
        $user = $request->user();
        $startDate = $request->get('start_date', now()->startOfYear());
        $endDate = $request->get('end_date', now()->endOfYear());

        $poultryExpenses = $user->poultryExpenses()
            ->with('category')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $cropExpenses = $user->cropInputExpenses()
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                'poultry_expenses' => $poultryExpenses->map(function ($expense) {
                    return [
                        'id' => $expense->id,
                        'date' => $expense->date,
                        'amount' => $expense->amount,
                        'description' => $expense->description,
                        'category' => $expense->category ? $expense->category->name : null,
                    ];
                }),
                'crop_expenses' => $cropExpenses->map(function ($expense) {
                    return [
                        'id' => $expense->id,
                        'date' => $expense->date,
                        'amount' => $expense->amount,
                        'description' => $expense->description,
                        'category' => 'Crop Input',
                    ];
                }),
                'summary' => [
                    'total_poultry_expenses' => $poultryExpenses->sum('amount'),
                    'total_crop_expenses' => $cropExpenses->sum('amount'),
                    'total_expenses' => $poultryExpenses->sum('amount') + $cropExpenses->sum('amount'),
                ]
            ]
        ]);
    }

    /**
     * Get profit/loss analysis
     */
    public function profitLoss(Request $request)
    {
        $user = $request->user();
        $year = $request->get('year', now()->year);

        $monthlyData = [];

        for ($month = 1; $month <= 12; $month++) {
            $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
            $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

            $eggSales = $user->eggSales()
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->sum(DB::raw('quantity_sold * price_per_unit'));

            $birdSales = $user->birdSales()
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->sum(DB::raw('quantity_sold * price_per_unit'));

            $totalSales = $eggSales + $birdSales;

            $poultryExpenses = $user->poultryExpenses()
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            $cropExpenses = $user->cropInputExpenses()
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            $totalExpenses = $poultryExpenses + $cropExpenses;
            $profitLoss = $totalSales - $totalExpenses;

            $monthlyData[] = [
                'month' => $startOfMonth->format('F'),
                'month_number' => $month,
                'sales' => $totalSales,
                'expenses' => $totalExpenses,
                'profit_loss' => $profitLoss,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'year' => $year,
                'monthly_data' => $monthlyData,
                'yearly_summary' => [
                    'total_sales' => array_sum(array_column($monthlyData, 'sales')),
                    'total_expenses' => array_sum(array_column($monthlyData, 'expenses')),
                    'total_profit_loss' => array_sum(array_column($monthlyData, 'profit_loss')),
                ]
            ]
        ]);
    }

    /**
     * Get egg production data
     */
    public function eggProduction(Request $request)
    {
        $user = $request->user();
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $productions = $user->eggProductions()
            ->with('birdBatch.farm')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                'productions' => $productions->map(function ($production) {
                    return [
                        'id' => $production->id,
                        'date' => $production->date,
                        'batch_code' => $production->birdBatch->batch_code ?? 'N/A',
                        'farm_name' => $production->birdBatch->farm->name ?? 'N/A',
                        'eggs_collected' => $production->eggs_collected,
                        'cracked_damaged' => $production->cracked_or_damaged,
                        'used_internal' => $production->eggs_used_internal,
                        'available' => $production->eggs_collected - $production->cracked_or_damaged - $production->eggs_used_internal,
                    ];
                }),
                'summary' => [
                    'total_productions' => $productions->count(),
                    'total_eggs_collected' => $productions->sum('eggs_collected'),
                    'total_available' => $productions->sum(function ($p) {
                        return $p->eggs_collected - $p->cracked_or_damaged - $p->eggs_used_internal;
                    }),
                ]
            ]
        ]);
    }

    /**
     * Get crop production data
     */
    public function cropProduction(Request $request)
    {
        $user = $request->user();
        $startDate = $request->get('start_date', now()->startOfYear());
        $endDate = $request->get('end_date', now()->endOfYear());

        $harvests = $user->harvests()
            ->with('planting.field.farm')
            ->whereBetween('harvest_date', [$startDate, $endDate])
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                'harvests' => $harvests->map(function ($harvest) {
                    return [
                        'id' => $harvest->id,
                        'harvest_date' => $harvest->harvest_date,
                        'field_name' => $harvest->planting->field->name ?? 'N/A',
                        'farm_name' => $harvest->planting->field->farm->name ?? 'N/A',
                        'crop_name' => $harvest->planting->crop_name ?? 'N/A',
                        'quantity_harvested' => $harvest->quantity_harvested,
                        'unit' => $harvest->unit,
                        'quality' => $harvest->quality,
                    ];
                }),
                'summary' => [
                    'total_harvests' => $harvests->count(),
                    'total_quantity' => $harvests->sum('quantity_harvested'),
                ]
            ]
        ]);
    }
}
