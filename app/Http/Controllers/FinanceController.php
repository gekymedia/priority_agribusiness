<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\EggSale;
use App\Models\BirdSale;
use App\Models\CropSale;
use App\Models\PoultryExpense;
use App\Models\SystemSetting;
use App\Services\PriorityBankApiClient;
use App\Services\PriorityBankIntegrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $manualQuery = Income::query();
        if ($from) {
            $manualQuery->where('received_on', '>=', $from);
        }
        if ($to) {
            $manualQuery->where('received_on', '<=', $to);
        }

        $manualIncomes = $manualQuery->get()->map(function ($income) {
            return (object) [
                'date' => $income->received_on,
                'entry_type' => 'income',
                'category' => $income->category,
                'description' => $income->description ?? '—',
                'amount' => (float) $income->amount,
                'external_transaction_id' => $income->external_transaction_id,
                'sync_route' => route('finance.income.sync', $income),
                'can_sync' => true,
                'source' => 'Manual',
            ];
        });

        $eggSalesQuery = EggSale::query();
        if ($from) {
            $eggSalesQuery->whereDate('date', '>=', $from);
        }
        if ($to) {
            $eggSalesQuery->whereDate('date', '<=', $to);
        }
        $eggSales = $eggSalesQuery->get()->map(function ($sale) {
            return (object) [
                'date' => $sale->date,
                'entry_type' => 'income',
                'category' => 'Egg Sales',
                'description' => trim("Egg sale: {$sale->quantity_sold} {$sale->unit_type}" . ($sale->buyer_name ? " - Buyer: {$sale->buyer_name}" : '')),
                'amount' => (float) ($sale->quantity_sold * $sale->price_per_unit),
                'external_transaction_id' => 'agri_egg_sale_' . $sale->id,
                'sync_route' => null,
                'can_sync' => false,
                'source' => 'Auto (Sales)',
            ];
        });

        $birdSalesQuery = BirdSale::query();
        if ($from) {
            $birdSalesQuery->whereDate('date', '>=', $from);
        }
        if ($to) {
            $birdSalesQuery->whereDate('date', '<=', $to);
        }
        $birdSales = $birdSalesQuery->get()->map(function ($sale) {
            return (object) [
                'date' => $sale->date,
                'entry_type' => 'income',
                'category' => 'Bird Sales',
                'description' => trim("Bird sale: {$sale->quantity_sold} birds" . ($sale->buyer_name ? " - Buyer: {$sale->buyer_name}" : '')),
                'amount' => (float) ($sale->quantity_sold * $sale->price_per_bird),
                'external_transaction_id' => 'agri_bird_sale_' . $sale->id,
                'sync_route' => null,
                'can_sync' => false,
                'source' => 'Auto (Sales)',
            ];
        });

        $cropSalesQuery = CropSale::query();
        if ($from) {
            $cropSalesQuery->whereDate('date', '>=', $from);
        }
        if ($to) {
            $cropSalesQuery->whereDate('date', '<=', $to);
        }
        $cropSales = $cropSalesQuery->get()->map(function ($sale) {
            return (object) [
                'date' => $sale->date,
                'entry_type' => 'income',
                'category' => 'Crop Sales',
                'description' => trim("Crop sale: {$sale->quantity_sold} units" . ($sale->buyer_name ? " - Buyer: {$sale->buyer_name}" : '')),
                'amount' => (float) ($sale->quantity_sold * $sale->price_per_unit),
                'external_transaction_id' => 'agri_crop_sale_' . $sale->id,
                'sync_route' => null,
                'can_sync' => false,
                'source' => 'Auto (Sales)',
            ];
        });

        $expenseQuery = PoultryExpense::with(['farm', 'expenseCategory']);
        if ($from) {
            $expenseQuery->where('date', '>=', $from);
        }
        if ($to) {
            $expenseQuery->where('date', '<=', $to);
        }
        $expenses = $expenseQuery->get()->map(function ($expense) {
            $categoryName = $expense->expenseCategory?->name ?? ($expense->getRawOriginal('category') ?: 'Uncategorized');
            return (object) [
                'date' => $expense->date,
                'entry_type' => 'expense',
                'category' => $categoryName,
                'description' => $expense->description ?? '—',
                'amount' => (float) $expense->amount,
                'external_transaction_id' => $expense->external_transaction_id,
                'sync_route' => route('finance.expenditure.sync', $expense),
                'can_sync' => true,
                'source' => 'Expense',
            ];
        });

        $rows = $manualIncomes
            ->concat($eggSales)
            ->concat($birdSales)
            ->concat($cropSales)
            ->concat($expenses)
            ->sortByDesc(function ($row) {
                return optional($row->date)->toDateString();
            })
            ->values();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;
        $items = $rows->forPage($page, $perPage)->values();
        $ledger = new LengthAwarePaginator(
            $items,
            $rows->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $incomeTotal = (float) $rows->where('entry_type', 'income')->sum('amount');
        $expenseTotal = (float) $rows->where('entry_type', 'expense')->sum('amount');
        $balance = $incomeTotal - $expenseTotal;

        return view('finance.index', compact('ledger', 'incomeTotal', 'expenseTotal', 'balance'));
    }

    public function incomeIndex(Request $request)
    {
        return redirect()->route('finance.index', $request->query());
    }

    public function incomeCreate()
    {
        return view('finance.income-create');
    }

    public function incomeStore(Request $request)
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'received_on' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
            'reference' => ['nullable', 'string', 'max:255'],
            'external_transaction_id' => ['nullable', 'string', 'max:64'],
        ]);
        Income::create($validated);
        return redirect()->route('finance.income.index')->with('success', 'Income record added successfully.');
    }

    public function incomeSync(Income $income)
    {
        $baseUrl = rtrim((string) (SystemSetting::get('priority_bank_api_url') ?: config('services.priority_bank.api_url', '')), '/');
        $token = SystemSetting::get('priority_bank_api_token') ?: config('services.priority_bank.api_token');
        $systemId = SystemSetting::get('priority_bank_system_id') ?: config('services.priority_bank.system_id', 'priority_agriculture');

        if (! $baseUrl || ! $token) {
            return redirect()->route('finance.income.index')
                ->with('error', 'Priority Bank sync is not configured. Set API URL and API Token in Settings → Priority Bank, or in .env.');
        }

        if (empty($income->external_transaction_id)) {
            $income->external_transaction_id = str_pad((string) random_int(0, 9999999999), 10, '0', STR_PAD_LEFT);
            $income->save();
        }

        $client = new PriorityBankApiClient($baseUrl, $token);
        $result = $client->pushIncome(
            $systemId,
            $income->external_transaction_id,
            (float) $income->amount,
            $income->received_on ? \Carbon\Carbon::parse($income->received_on)->format('Y-m-d') : null,
            'bank',
            [
                'notes' => $income->description ?? $income->category,
                'income_category_name' => $income->category,
            ]
        );

        if ($result && ($result['success'] ?? false)) {
            return redirect()->route('finance.income.index')->with('success', 'Income synced to Priority Bank successfully.');
        }
        Log::warning('Priority Bank income sync failed', ['income_id' => $income->id, 'response' => $result]);
        return redirect()->route('finance.income.index')->with('error', 'Bank sync failed. Check Settings → Priority Bank and try again.');
    }

    public function expenditureIndex(Request $request)
    {
        return redirect()->route('finance.index', $request->query());
    }

    public function expenditureSync(PoultryExpense $expense)
    {
        $baseUrl = rtrim((string) (SystemSetting::get('priority_bank_api_url') ?: config('services.priority_bank.api_url', '')), '/');
        $token = SystemSetting::get('priority_bank_api_token') ?: config('services.priority_bank.api_token');
        $systemId = SystemSetting::get('priority_bank_system_id') ?: config('services.priority_bank.system_id', 'priority_agriculture');

        if (! $baseUrl || ! $token) {
            return redirect()->route('finance.expenditure.index')
                ->with('error', 'Priority Bank sync is not configured. Set API URL and API Token in Settings → Priority Bank, or in .env.');
        }

        $extId = $expense->external_transaction_id;
        if (empty($extId)) {
            $extId = str_pad((string) random_int(0, 9999999999), 10, '0', STR_PAD_LEFT);
            $expense->external_transaction_id = $extId;
            $expense->save();
        }

        $client = new PriorityBankApiClient($baseUrl, $token);
        $legacyCategory = $expense->getRawOriginal('category');
        $categoryName = $expense->expenseCategory?->name ?? $legacyCategory ?? $expense->category ?? 'Expense';
        $result = $client->pushExpense(
            $systemId,
            $extId,
            (float) $expense->amount,
            $expense->date ? \Carbon\Carbon::parse($expense->date)->format('Y-m-d') : null,
            'bank',
            [
                'notes' => $expense->description ?? $categoryName,
                'expense_category_name' => $categoryName,
            ]
        );

        if ($result && ($result['success'] ?? false)) {
            return redirect()->route('finance.expenditure.index')->with('success', 'Expenditure synced to Priority Bank successfully.');
        }
        Log::warning('Priority Bank expenditure sync failed', ['expense_id' => $expense->id, 'response' => $result]);
        return redirect()->route('finance.expenditure.index')->with('error', 'Bank sync failed. Check Settings → Priority Bank and try again.');
    }
}
