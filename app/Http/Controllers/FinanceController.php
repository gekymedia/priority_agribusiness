<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\PoultryExpense;
use App\Models\SystemSetting;
use App\Services\PriorityBankApiClient;
use App\Services\PriorityBankIntegrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FinanceController extends Controller
{
    public function incomeIndex(Request $request)
    {
        $query = Income::query();
        if ($request->filled('from')) {
            $query->where('received_on', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->where('received_on', '<=', $request->input('to'));
        }
        $incomes = $query->latest('received_on')->paginate(20);
        $total = (clone $query)->sum('amount');
        return view('finance.income-index', compact('incomes', 'total'));
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
            $income->received_on?->format('Y-m-d'),
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
        $query = PoultryExpense::with(['farm', 'birdBatch', 'expenseCategory']);
        if ($request->filled('from')) {
            $query->where('date', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->where('date', '<=', $request->input('to'));
        }
        $expenses = $query->latest('date')->paginate(20);
        $total = (clone $query)->sum('amount');
        return view('finance.expenditure-index', compact('expenses', 'total'));
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
            $expense->date?->format('Y-m-d'),
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
