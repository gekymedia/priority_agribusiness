<?php

namespace App\Http\Controllers;

use App\Models\PoultryExpense;
use App\Models\ExpenseCategory;
use App\Models\Farm;
use App\Models\BirdBatch;
use App\Services\PriorityBankIntegrationService;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = PoultryExpense::with(['farm', 'birdBatch', 'category'])
            ->latest('date')
            ->paginate(15);
        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        $farms = Farm::all();
        $batches = BirdBatch::with('farm')->get();
        $categories = ExpenseCategory::where('is_active', true)->get();
        return view('expenses.create', compact('farms', 'batches', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'bird_batch_id' => 'nullable|exists:bird_batches,id',
            'category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $expense = PoultryExpense::create($data);

        // Push to Priority Bank
        try {
            $integrationService = new PriorityBankIntegrationService();
            $integrationService->pushPoultryExpense($expense);
        } catch (\Exception $e) {
            \Log::error('Failed to push expense to Priority Bank', [
                'expense_id' => $expense->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully.');
    }

    public function show(PoultryExpense $expense)
    {
        $expense->load(['farm', 'birdBatch', 'category']);
        return view('expenses.show', compact('expense'));
    }

    public function edit(PoultryExpense $expense)
    {
        $farms = Farm::all();
        $batches = BirdBatch::with('farm')->get();
        $categories = ExpenseCategory::where('is_active', true)->get();
        $expense->load(['farm', 'birdBatch', 'category']);
        return view('expenses.edit', compact('expense', 'farms', 'batches', 'categories'));
    }

    public function update(Request $request, PoultryExpense $expense)
    {
        $data = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'bird_batch_id' => 'nullable|exists:bird_batches,id',
            'category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $expense->update($data);

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(PoultryExpense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }
}
