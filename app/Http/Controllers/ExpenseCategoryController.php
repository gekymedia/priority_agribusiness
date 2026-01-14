<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = ExpenseCategory::latest()->paginate(15);
        return view('expense-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('expense-categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name',
            'type' => 'required|string|in:poultry,crop,general',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        ExpenseCategory::create($data);

        return redirect()->route('expense-categories.index')->with('success', 'Expense category created successfully.');
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        return view('expense-categories.edit', compact('expenseCategory'));
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $expenseCategory->id,
            'type' => 'required|string|in:poultry,crop,general',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $expenseCategory->update($data);

        return redirect()->route('expense-categories.index')->with('success', 'Expense category updated successfully.');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->expenses()->count() > 0) {
            return redirect()->route('expense-categories.index')
                ->with('error', 'Cannot delete category with existing expenses.');
        }

        $expenseCategory->delete();
        return redirect()->route('expense-categories.index')->with('success', 'Expense category deleted successfully.');
    }
}
