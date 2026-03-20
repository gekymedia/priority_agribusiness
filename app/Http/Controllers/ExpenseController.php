<?php

namespace App\Http\Controllers;

use App\Models\PoultryExpense;
use App\Models\ExpenseCategory;
use App\Models\Farm;
use App\Models\BirdBatch;
use App\Services\PriorityBankIntegrationService;
use App\Services\CrudNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->query('sort', 'date');
        $direction = strtolower($request->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['date', 'farm', 'batch', 'category', 'description', 'amount'];
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'date';
        }

        $query = PoultryExpense::query()->with(['farm', 'birdBatch', 'expenseCategory']);

        switch ($sort) {
            case 'date':
                $query->orderBy('date', $direction);
                break;
            case 'amount':
                $query->orderBy('amount', $direction);
                break;
            case 'description':
                $query->orderBy('description', $direction);
                break;
            case 'farm':
                $query->leftJoin('farms', 'poultry_expenses.farm_id', '=', 'farms.id')
                    ->select('poultry_expenses.*')
                    ->orderBy('farms.name', $direction);
                break;
            case 'batch':
                $query->leftJoin('bird_batches', 'poultry_expenses.bird_batch_id', '=', 'bird_batches.id')
                    ->select('poultry_expenses.*')
                    ->orderBy('bird_batches.batch_code', $direction);
                break;
            case 'category':
                $query->leftJoin('expense_categories', 'poultry_expenses.category_id', '=', 'expense_categories.id')
                    ->select('poultry_expenses.*')
                    ->orderBy('expense_categories.name', $direction);
                break;
        }

        $expenses = $query->paginate(50)->withQueryString();

        $todayTotal = (float) PoultryExpense::whereRaw('DATE(`date`) = ?', [Carbon::today()->toDateString()])->sum('amount');
        $yesterdayTotal = (float) PoultryExpense::whereRaw('DATE(`date`) = ?', [Carbon::yesterday()->toDateString()])->sum('amount');
        $monthTotal = (float) PoultryExpense::whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->sum('amount');

        return view('expenses.index', compact(
            'expenses',
            'sort',
            'direction',
            'todayTotal',
            'yesterdayTotal',
            'monthTotal'
        ));
    }

    public function create()
    {
        $farms = Farm::all();
        $batches = BirdBatch::with('farm')->get();
        $categories = ExpenseCategory::where('is_active', true)->get();
        return view('expenses.create', compact('farms', 'batches', 'categories'));
    }

    public function bulkAdd()
    {
        $farms = Farm::all();
        $batches = BirdBatch::with('farm')->get();
        $categories = ExpenseCategory::where('is_active', true)->get();
        return view('expenses.bulk-add', compact('farms', 'batches', 'categories'));
    }

    public function storeBulk(Request $request)
    {
        $data = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'bird_batch_id' => 'nullable|exists:bird_batches,id',
            'category_id' => 'required|exists:expense_categories,id',
            'default_date' => 'required|date',
            'pasted_data' => 'required|string|max:50000',
        ]);

        $parsed = $this->parseBulkExpenseText(
            $data['pasted_data'],
            $data['default_date'],
            (int) $data['category_id'],
            ExpenseCategory::where('is_active', true)->get()
        );

        if (empty($parsed)) {
            return redirect()
                ->route('expenses.bulk-add')
                ->withInput()
                ->with('error', 'No valid expense lines found. Use bracket format [date, description, amount, category] or tab/comma lines: date (or —), description, amount.');
        }

        $farmId = (int) $data['farm_id'];
        $batchId = $data['bird_batch_id'] ? (int) $data['bird_batch_id'] : null;
        $created = 0;
        $errors = [];

        foreach ($parsed as $index => $row) {
            try {
                $expense = PoultryExpense::create([
                    'farm_id' => $farmId,
                    'bird_batch_id' => $batchId,
                    'category_id' => $row['category_id'],
                    'date' => $row['date'],
                    'amount' => $row['amount'],
                    'description' => $row['description'],
                ]);

                try {
                    (new PriorityBankIntegrationService())->pushPoultryExpense($expense);
                } catch (\Exception $e) {
                    \Log::error('Failed to push bulk expense to Priority Bank', [
                        'expense_id' => $expense->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                app(CrudNotificationService::class)->notify('expenses', 'created', $expense, auth()->user());
                $created++;
            } catch (\Exception $e) {
                $errors[] = 'Row ' . ($index + 1) . ': ' . $e->getMessage();
            }
        }

        $message = $created . ' expense(s) added successfully.';
        if (! empty($errors)) {
            $message .= ' Failed: ' . implode('; ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $message .= ' (+' . (count($errors) - 5) . ' more)';
            }
        }

        return redirect()
            ->route('expenses.index')
            ->with($created > 0 ? 'success' : 'error', $message);
    }

    /**
     * Parse pasted bulk expense text: supports bracket format [date, description, amount, category]
     * and tab/comma-separated lines. Returns array of rows for create.
     */
    protected function parseBulkExpenseText(string $text, string $defaultDate, int $defaultCategoryId, $categories): array
    {
        $bracketRows = $this->parseBulkExpenseBracketFormat($text, $defaultDate, $defaultCategoryId, $categories);
        if (! empty($bracketRows)) {
            return $bracketRows;
        }
        return $this->parseBulkExpenseLineFormat($text, $defaultDate, $defaultCategoryId, $categories);
    }

    /**
     * Bracket format: each [date, description, amount, category] = one expense row.
     */
    protected function parseBulkExpenseBracketFormat(string $text, string $defaultDate, int $defaultCategoryId, $categories): array
    {
        if (! preg_match_all('/\[([^\]]*)\]/', $text, $matches)) {
            return [];
        }
        $categoryByName = $categories->keyBy(fn ($c) => strtolower($c->name));
        $defaultDateFormatted = Carbon::parse($defaultDate)->format('Y-m-d');
        $result = [];

        foreach ($matches[1] as $inner) {
            $parts = array_map('trim', explode(',', $inner));
            if (count($parts) < 3) {
                continue;
            }
            $dateRaw = $parts[0];
            if (count($parts) === 3) {
                $description = $parts[1];
                $amountRaw = $parts[2];
                $categoryName = null;
            } else {
                $categoryName = trim($parts[count($parts) - 1]);
                $amountRaw = trim($parts[count($parts) - 2]);
                $description = trim(implode(',', array_slice($parts, 1, count($parts) - 3)));
            }

            if ($description === '' || $amountRaw === '') {
                continue;
            }

            $amount = $this->parseAmount($amountRaw);
            if ($amount === null) {
                continue;
            }

            if ($this->isDefaultDatePlaceholder($dateRaw)) {
                $date = $defaultDateFormatted;
            } else {
                try {
                    $date = Carbon::parse($dateRaw)->format('Y-m-d');
                } catch (\Exception $e) {
                    $date = $defaultDateFormatted;
                }
            }

            $categoryId = $defaultCategoryId;
            if ($categoryName !== null && $categoryName !== '') {
                $found = $categoryByName->get(strtolower($categoryName));
                if ($found) {
                    $categoryId = $found->id;
                }
            }

            $result[] = [
                'date' => $date,
                'description' => $description,
                'amount' => $amount,
                'category_id' => $categoryId,
            ];
        }

        return $result;
    }

    /**
     * Tab/comma line format: one line per expense. First line can be header.
     */
    protected function parseBulkExpenseLineFormat(string $text, string $defaultDate, int $defaultCategoryId, $categories): array
    {
        $lines = array_filter(array_map('trim', explode("\n", $text)));
        $categoryByName = $categories->keyBy(fn ($c) => strtolower($c->name));
        $defaultDateFormatted = Carbon::parse($defaultDate)->format('Y-m-d');
        $result = [];

        foreach ($lines as $line) {
            $cols = preg_split('/\t+|,\s*/', $line, -1, PREG_SPLIT_NO_EMPTY);
            $cols = array_map('trim', $cols);

            if (count($cols) < 2) {
                continue;
            }

            if ($this->looksLikeHeader($cols)) {
                continue;
            }

            $dateRaw = $cols[0] ?? '';
            $description = $cols[1] ?? '';
            $amountRaw = $cols[2] ?? '';
            $categoryName = $cols[3] ?? null;

            if ($description === '' || $amountRaw === '') {
                continue;
            }

            $amount = $this->parseAmount($amountRaw);
            if ($amount === null) {
                continue;
            }

            if ($this->isDefaultDatePlaceholder($dateRaw)) {
                $date = $defaultDateFormatted;
            } else {
                try {
                    $date = Carbon::parse($dateRaw)->format('Y-m-d');
                } catch (\Exception $e) {
                    $date = $defaultDateFormatted;
                }
            }

            $categoryId = $defaultCategoryId;
            if ($categoryName !== null && $categoryName !== '') {
                $found = $categoryByName->get(strtolower($categoryName));
                if ($found) {
                    $categoryId = $found->id;
                }
            }

            $result[] = [
                'date' => $date,
                'description' => $description,
                'amount' => $amount,
                'category_id' => $categoryId,
            ];
        }

        return $result;
    }

    protected function looksLikeHeader(array $cols): bool
    {
        $first = strtolower($cols[0] ?? '');
        $second = strtolower($cols[1] ?? '');
        if (str_contains($first, 'date') || str_contains($second, 'description')) {
            return true;
        }
        if (str_contains($first, 'amount') || str_contains($second, 'amount')) {
            return true;
        }
        return false;
    }

    protected function isDefaultDatePlaceholder(string $value): bool
    {
        $v = trim($value);
        return $v === '' || $v === '—' || $v === '-' || strtolower($v) === 'same' || strtolower($v) === 'same day';
    }

    protected function parseAmount(string $value): ?float
    {
        $cleaned = preg_replace('/[^\d.]/', '', $value);
        if ($cleaned === '') {
            return null;
        }
        $float = (float) $cleaned;
        return $float >= 0 ? $float : null;
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

        app(CrudNotificationService::class)->notify('expenses', 'created', $expense, auth()->user());

        return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully.');
    }

    public function show(PoultryExpense $expense)
    {
        $expense->load(['farm', 'birdBatch', 'expenseCategory']);
        return view('expenses.show', compact('expense'));
    }

    public function edit(PoultryExpense $expense)
    {
        $farms = Farm::all();
        $batches = BirdBatch::with('farm')->get();
        $categories = ExpenseCategory::where('is_active', true)->get();
        $expense->load(['farm', 'birdBatch', 'expenseCategory']);
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

        app(CrudNotificationService::class)->notify('expenses', 'updated', $expense, auth()->user());

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(PoultryExpense $expense)
    {
        $recordCopy = clone $expense;
        $expense->delete();

        app(CrudNotificationService::class)->notify('expenses', 'deleted', $recordCopy, auth()->user());

        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }
}
