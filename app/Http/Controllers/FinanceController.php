<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\EggSale;
use App\Models\EggClientSale;
use App\Models\BirdSale;
use App\Models\CropSale;
use App\Models\CropInputExpense;
use App\Models\PoultryExpense;
use App\Services\PriorityBankSyncService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class FinanceController extends Controller
{
    public function index(Request $request, PriorityBankSyncService $syncService)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $manualIncomes = Income::query()
            ->when($from, fn ($q) => $q->whereDate('received_on', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('received_on', '<=', $to))
            ->get()
            ->map(fn ($income) => $this->ledgerRow(
                date: $income->received_on,
                entryType: 'income',
                category: $income->category,
                description: $income->description ?? '—',
                amount: (float) $income->amount,
                externalId: $income->external_transaction_id,
                source: 'Manual',
                syncedAt: $income->priority_bank_synced_at ?? null,
                syncRoute: route('finance.income.sync', $income),
            ));

        $eggClientSales = EggClientSale::query()
            ->with('items')
            ->when($from, fn ($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('date', '<=', $to))
            ->get()
            ->map(function ($sale) {
                $received = (float) $sale->amount_paid;
                $description = 'Egg client sale' . ($sale->buyer_name ? " - {$sale->buyer_name}" : '');
                if ($received > 0 && $received < $sale->total_amount) {
                    $description .= ' (received ₵' . number_format($received, 2) . ' of ₵' . number_format($sale->total_amount, 2) . ')';
                }

                return $this->ledgerRow(
                    date: $sale->date,
                    entryType: 'income',
                    category: 'Egg Sales',
                    description: $description,
                    amount: (float) $sale->total_amount,
                    externalId: 'agri_egg_client_sale_' . $sale->id,
                    source: 'Auto (Egg Sales)',
                    syncedAt: $sale->priority_bank_synced_at ?? null,
                    syncRoute: null,
                    canSync: $received > 0,
                );
            });

        $legacyEggSales = EggSale::query()
            ->whereNull('egg_client_sale_id')
            ->when($from, fn ($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('date', '<=', $to))
            ->get()
            ->map(fn ($sale) => $this->ledgerRow(
                date: $sale->date,
                entryType: 'income',
                category: 'Egg Sales',
                description: trim("Egg sale: {$sale->quantity_sold} {$sale->unit_type}" . ($sale->buyer_name ? " - Buyer: {$sale->buyer_name}" : '')),
                amount: (float) ($sale->quantity_sold * $sale->price_per_unit),
                externalId: 'agri_egg_sale_' . $sale->id,
                source: 'Auto (Egg Sales)',
                syncedAt: $sale->priority_bank_synced_at ?? null,
                syncRoute: null,
            ));

        $birdSales = BirdSale::query()
            ->when($from, fn ($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('date', '<=', $to))
            ->get()
            ->map(fn ($sale) => $this->ledgerRow(
                date: $sale->date,
                entryType: 'income',
                category: 'Bird Sales',
                description: trim("Bird sale: {$sale->quantity_sold} birds" . ($sale->buyer_name ? " - Buyer: {$sale->buyer_name}" : '')),
                amount: (float) ($sale->quantity_sold * $sale->price_per_bird),
                externalId: 'agri_bird_sale_' . $sale->id,
                source: 'Auto (Bird Sales)',
                syncedAt: $sale->priority_bank_synced_at ?? null,
                syncRoute: null,
            ));

        $cropSales = CropSale::query()
            ->when($from, fn ($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('date', '<=', $to))
            ->get()
            ->map(fn ($sale) => $this->ledgerRow(
                date: $sale->date,
                entryType: 'income',
                category: 'Crop Sales',
                description: trim("Crop sale: {$sale->quantity_sold} units" . ($sale->buyer_name ? " - Buyer: {$sale->buyer_name}" : '')),
                amount: (float) ($sale->quantity_sold * $sale->price_per_unit),
                externalId: 'agri_crop_sale_' . $sale->id,
                source: 'Auto (Crop Sales)',
                syncedAt: $sale->priority_bank_synced_at ?? null,
                syncRoute: null,
            ));

        $poultryExpenses = PoultryExpense::query()
            ->with(['farm', 'expenseCategory'])
            ->when($from, fn ($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('date', '<=', $to))
            ->get()
            ->map(function ($expense) {
                $categoryName = $expense->expenseCategory?->name ?? ($expense->getRawOriginal('category') ?: 'Uncategorized');

                return $this->ledgerRow(
                    date: $expense->date,
                    entryType: 'expense',
                    category: $categoryName,
                    description: $expense->description ?? '—',
                    amount: (float) $expense->amount,
                    externalId: $expense->external_transaction_id ?? ('agri_poultry_expense_' . $expense->id),
                    source: 'Poultry Expense',
                    syncedAt: $expense->priority_bank_synced_at ?? null,
                    syncRoute: route('finance.expenditure.sync', $expense),
                );
            });

        $cropExpenses = CropInputExpense::query()
            ->when($from, fn ($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('date', '<=', $to))
            ->get()
            ->map(fn ($expense) => $this->ledgerRow(
                date: $expense->date,
                entryType: 'expense',
                category: $expense->category ?? 'Crop Input',
                description: $expense->description ?? '—',
                amount: (float) $expense->amount,
                externalId: 'agri_crop_expense_' . $expense->id,
                source: 'Crop Expense',
                syncedAt: $expense->priority_bank_synced_at ?? null,
                syncRoute: null,
            ));

        $rows = $manualIncomes
            ->concat($eggClientSales)
            ->concat($legacyEggSales)
            ->concat($birdSales)
            ->concat($cropSales)
            ->concat($poultryExpenses)
            ->concat($cropExpenses)
            ->sortByDesc(fn ($row) => optional($row->date)->toDateString())
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
        $pendingSync = $syncService->countPending($from, $to);
        $bankConfigured = $syncService->isConfigured();

        return view('finance.index', compact(
            'ledger',
            'incomeTotal',
            'expenseTotal',
            'balance',
            'pendingSync',
            'bankConfigured',
        ));
    }

    public function bulkSync(Request $request, PriorityBankSyncService $syncService)
    {
        if (! $syncService->isConfigured()) {
            return redirect()->route('finance.index', $request->only(['from', 'to']))
                ->with('error', 'Priority Bank sync is not configured. Set API URL and API Token in Settings → Priority Bank.');
        }

        $results = $syncService->syncAll($request->input('from'), $request->input('to'));

        if (($results['synced'] ?? 0) === 0 && ($results['failed'] ?? 0) === 0) {
            return redirect()->route('finance.index', $request->only(['from', 'to']))
                ->with('info', 'All records in this range are already synced to Priority Bank.');
        }

        if (! empty($results['errors']) && ($results['synced'] ?? 0) === 0) {
            return redirect()->route('finance.index', $request->only(['from', 'to']))
                ->with('error', $results['errors'][0]);
        }

        $message = ($results['synced'] ?? 0) . ' record(s) synced to Priority Bank.';
        if (($results['failed'] ?? 0) > 0) {
            $message .= ' ' . $results['failed'] . ' failed.';
        }

        return redirect()->route('finance.index', $request->only(['from', 'to']))
            ->with(($results['synced'] ?? 0) > 0 ? 'success' : 'info', $message);
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

    public function incomeSync(Income $income, PriorityBankSyncService $syncService)
    {
        if (! $syncService->isConfigured()) {
            return redirect()->route('finance.index')
                ->with('error', 'Priority Bank sync is not configured. Set API URL and API Token in Settings → Priority Bank.');
        }

        if ($syncService->syncIncome($income)) {
            return redirect()->route('finance.index')->with('success', 'Income synced to Priority Bank successfully.');
        }

        return redirect()->route('finance.index')->with('error', 'Bank sync failed. Check Settings → Priority Bank and try again.');
    }

    public function expenditureIndex(Request $request)
    {
        return redirect()->route('finance.index', $request->query());
    }

    public function expenditureSync(PoultryExpense $expense, PriorityBankSyncService $syncService)
    {
        if (! $syncService->isConfigured()) {
            return redirect()->route('finance.index')
                ->with('error', 'Priority Bank sync is not configured. Set API URL and API Token in Settings → Priority Bank.');
        }

        if ($syncService->syncPoultryExpense($expense)) {
            return redirect()->route('finance.index')->with('success', 'Expenditure synced to Priority Bank successfully.');
        }

        return redirect()->route('finance.index')->with('error', 'Bank sync failed. Check Settings → Priority Bank and try again.');
    }

    protected function ledgerRow(
        $date,
        string $entryType,
        string $category,
        string $description,
        float $amount,
        ?string $externalId,
        string $source,
        $syncedAt,
        ?string $syncRoute = null,
        bool $canSync = true,
    ): object {
        $bankSynced = $syncedAt !== null;

        return (object) [
            'date' => $date,
            'entry_type' => $entryType,
            'category' => $category,
            'description' => $description,
            'amount' => $amount,
            'external_transaction_id' => $externalId,
            'source' => $source,
            'bank_synced' => $bankSynced,
            'sync_route' => $syncRoute,
            'can_sync' => $canSync && ! $bankSynced && ($syncRoute !== null || in_array($source, [
                'Auto (Egg Sales)',
                'Auto (Bird Sales)',
                'Auto (Crop Sales)',
                'Crop Expense',
            ], true)),
        ];
    }
}
