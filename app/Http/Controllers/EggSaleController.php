<?php

namespace App\Http\Controllers;

use App\Models\EggClientSale;
use App\Models\EggSale;
use App\Models\BirdBatch;
use App\Models\MarketOrder;
use App\Models\PaymentSetting;
use App\Services\PriorityBankIntegrationService;
use App\Services\CrudNotificationService;
use App\Services\EggClientSaleRecorder;
use App\Services\EggSaleBulkImportParser;
use App\Services\EggStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EggSaleController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->query('sort', 'date');
        $direction = strtolower($request->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['date', 'batch', 'farm', 'buyer_name', 'total_amount', 'amount_paid', 'payment_status'];
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'date';
        }

        $clientSalesQuery = EggClientSale::query()->with(['birdBatch.farm', 'items']);
        switch ($sort) {
            case 'date':
                $clientSalesQuery->orderBy('date', $direction);
                break;
            case 'buyer_name':
                $clientSalesQuery->orderBy('buyer_name', $direction);
                break;
            case 'amount_paid':
                $clientSalesQuery->orderBy('amount_paid', $direction);
                break;
            case 'batch':
                $clientSalesQuery->leftJoin('bird_batches', 'egg_client_sales.bird_batch_id', '=', 'bird_batches.id')
                    ->select('egg_client_sales.*')
                    ->orderBy('bird_batches.batch_code', $direction);
                break;
            case 'farm':
                $clientSalesQuery->leftJoin('bird_batches as bb', 'egg_client_sales.bird_batch_id', '=', 'bb.id')
                    ->leftJoin('farms', 'bb.farm_id', '=', 'farms.id')
                    ->select('egg_client_sales.*')
                    ->orderBy('farms.name', $direction);
                break;
            default:
                $clientSalesQuery->orderBy('date', $direction);
                break;
        }

        $clientSales = $clientSalesQuery->paginate(50)->withQueryString();

        $legacySalesQuery = EggSale::query()
            ->whereNull('egg_client_sale_id')
            ->with('birdBatch.farm');
        if ($sort === 'date') {
            $legacySalesQuery->orderBy('date', $direction);
        } elseif ($sort === 'buyer_name') {
            $legacySalesQuery->orderBy('buyer_name', $direction);
        } else {
            $legacySalesQuery->orderBy('date', $direction);
        }
        $legacySales = $legacySalesQuery->paginate(50, ['*'], 'legacy_page')->withQueryString();

        $onlineSort = $request->query('online_sort', 'created_at');
        $onlineDir = strtolower($request->query('online_direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        $onlineAllowed = ['order_number', 'created_at', 'customer_name', 'total_amount', 'status'];
        if (! in_array($onlineSort, $onlineAllowed, true)) {
            $onlineSort = 'created_at';
        }
        $onlineQuery = MarketOrder::query()->with('items')->orderBy($onlineSort, $onlineDir);
        $onlineOrders = $onlineQuery->paginate(50, ['*'], 'online_page')->withQueryString();
        if (request('tab') === 'online') {
            $onlineOrders->appends(['tab' => 'online']);
        }

        $eggStock = app(EggStockService::class)->summary();

        return view('egg-sales.index', compact('clientSales', 'legacySales', 'onlineOrders', 'sort', 'direction', 'onlineSort', 'onlineDir', 'eggStock'));
    }

    /** Mark an online order as complete (eggs given to customer) and record in egg sales. */
    public function markOrderComplete(MarketOrder $market_order)
    {
        $order = $market_order;
        if ($order->status === MarketOrder::STATUS_DELIVERED) {
            return redirect()->route('egg-sales.index', ['tab' => 'online'])->with('info', 'Order already marked complete.');
        }
        if ($order->status !== MarketOrder::STATUS_PAID) {
            return redirect()->route('egg-sales.index', ['tab' => 'online'])->with('error', 'Only paid orders can be marked complete.');
        }

        $batchId = PaymentSetting::getEggMarketBatchId();
        if (! $batchId || ! BirdBatch::find($batchId)) {
            return redirect()->route('egg-sales.index', ['tab' => 'online'])->with('error', 'Egg market batch is not set in Payment Settings. Please set it first.');
        }

        foreach ($order->items as $item) {
            EggSale::create([
                'market_order_id' => $order->id,
                'bird_batch_id' => $batchId,
                'date' => $order->created_at->toDateString(),
                'quantity_sold' => $item->quantity,
                'unit_type' => $item->unit_type,
                'price_per_unit' => $item->unit_price,
                'payment_status' => EggSale::PAYMENT_PAID,
                'buyer_name' => $order->customer_name,
                'buyer_contact' => $order->customer_phone,
                'notes' => 'Online order ' . $order->order_number,
            ]);
        }

        $order->update(['status' => MarketOrder::STATUS_DELIVERED]);

        try {
            $integrationService = new PriorityBankIntegrationService();
            foreach (EggSale::where('market_order_id', $order->id)->get() as $eggSale) {
                $integrationService->pushEggSale($eggSale);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to push egg sale to Priority Bank', ['order_id' => $order->id, 'error' => $e->getMessage()]);
        }

        return redirect()->route('egg-sales.index', ['tab' => 'online'])->with('success', 'Order marked complete and recorded in egg sales.');
    }

    public function create()
    {
        $batches = BirdBatch::whereIn('purpose', ['egg_production', 'layer'])
            ->with('farm')
            ->get();

        $eggStock = app(EggStockService::class)->summary();

        return view('egg-sales.create', compact('batches', 'eggStock'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bird_batch_id' => 'required|exists:bird_batches,id',
            'date' => 'required|date',
            'buyer_name' => 'nullable|string|max:255',
            'buyer_contact' => 'nullable|string|max:255',
            'amount_paid' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.egg_size' => 'required|string|in:small,medium,large',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price_per_unit' => 'required|numeric|min:0',
            'items.*.payment_status' => 'required|string|in:paid,unpaid',
            'items.*.line_notes' => 'nullable|string|max:255',
        ]);

        $clientSale = app(EggClientSaleRecorder::class)->record($data);

        try {
            $integrationService = new PriorityBankIntegrationService();
            $integrationService->pushEggClientSale($clientSale);
        } catch (\Exception $e) {
            \Log::error('Failed to push egg client sale to Priority Bank', [
                'egg_client_sale_id' => $clientSale->id,
                'error' => $e->getMessage(),
            ]);
        }

        app(CrudNotificationService::class)->notify('egg_sales', 'created', $clientSale, auth()->user());

        return redirect()->route('egg-sales.show', $clientSale)->with('success', 'Egg sale recorded successfully.');
    }

    public function show(EggClientSale $eggSale)
    {
        $eggSale->load(['birdBatch.farm', 'items']);

        return view('egg-sales.show', ['clientSale' => $eggSale]);
    }

    public function edit(EggClientSale $eggSale)
    {
        $eggSale->load('items');
        $batches = BirdBatch::whereIn('purpose', ['egg_production', 'layer'])
            ->with('farm')
            ->get();

        return view('egg-sales.edit', ['clientSale' => $eggSale, 'batches' => $batches]);
    }

    public function update(Request $request, EggClientSale $eggSale)
    {
        $data = $request->validate([
            'bird_batch_id' => 'required|exists:bird_batches,id',
            'date' => 'required|date',
            'buyer_name' => 'nullable|string|max:255',
            'buyer_contact' => 'nullable|string|max:255',
            'amount_paid' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.egg_size' => 'required|string|in:small,medium,large',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price_per_unit' => 'required|numeric|min:0',
            'items.*.payment_status' => 'required|string|in:paid,unpaid',
            'items.*.line_notes' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($data, $eggSale) {
            $eggSale->update([
                'bird_batch_id' => $data['bird_batch_id'],
                'date' => $data['date'],
                'buyer_name' => $data['buyer_name'] ?? null,
                'buyer_contact' => $data['buyer_contact'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $eggSale->items()->delete();

            $computedPaid = 0.0;
            foreach ($data['items'] as $item) {
                $lineTotal = round($item['quantity'] * $item['price_per_unit'], 2);
                if ($item['payment_status'] === EggSale::PAYMENT_PAID) {
                    $computedPaid += $lineTotal;
                }

                EggSale::create([
                    'egg_client_sale_id' => $eggSale->id,
                    'bird_batch_id' => $data['bird_batch_id'],
                    'date' => $data['date'],
                    'quantity_sold' => $item['quantity'],
                    'unit_type' => 'piece',
                    'egg_size' => $item['egg_size'],
                    'price_per_unit' => $item['price_per_unit'],
                    'payment_status' => $item['payment_status'],
                    'buyer_name' => $data['buyer_name'] ?? null,
                    'buyer_contact' => $data['buyer_contact'] ?? null,
                    'notes' => $item['line_notes'] ?? null,
                ]);
            }

            $amountPaid = array_key_exists('amount_paid', $data) && $data['amount_paid'] !== null
                ? (float) $data['amount_paid']
                : $computedPaid;

            $eggSale->update(['amount_paid' => $amountPaid]);
        });

        app(CrudNotificationService::class)->notify('egg_sales', 'updated', $eggSale->fresh(['items']), auth()->user());

        return redirect()->route('egg-sales.show', $eggSale)->with('success', 'Egg sale updated successfully.');
    }

    public function destroy(EggClientSale $eggSale)
    {
        $recordCopy = clone $eggSale->load('items');
        $eggSale->delete();

        app(CrudNotificationService::class)->notify('egg_sales', 'deleted', $recordCopy, auth()->user());

        return redirect()->route('egg-sales.index')->with('success', 'Egg sale deleted successfully.');
    }

    public function bulkImport()
    {
        $batches = BirdBatch::whereIn('purpose', ['egg_production', 'layer'])
            ->with('farm')
            ->orderBy('arrival_date', 'desc')
            ->get();

        return view('egg-sales.bulk-import', compact('batches'));
    }

    public function processBulkImport(Request $request)
    {
        $data = $request->validate([
            'bird_batch_id' => 'required|exists:bird_batches,id',
            'date' => 'required|date',
            'pasted_data' => 'required|string|max:50000',
            'skip_duplicates' => 'nullable|boolean',
        ]);

        $parsed = app(EggSaleBulkImportParser::class)->parse($data['pasted_data']);
        if (empty($parsed)) {
            return redirect()->route('egg-sales.bulk-import')
                ->withInput()
                ->with('error', 'No valid client sales found. Paste a sales report (sections A, B, C…) or use bracket format: [Client name | received: 703] followed by size, qty, price lines.');
        }

        $batchId = (int) $data['bird_batch_id'];
        $saleDate = $data['date'];
        $skipDuplicates = $request->boolean('skip_duplicates', true);
        $recorder = app(EggClientSaleRecorder::class);
        $integrationService = new PriorityBankIntegrationService();
        $created = 0;
        $skipped = 0;
        $errors = [];

        foreach ($parsed as $clientData) {
            $buyerName = $clientData['buyer_name'];

            if ($skipDuplicates && $buyerName !== '') {
                $exists = EggClientSale::query()
                    ->where('bird_batch_id', $batchId)
                    ->whereDate('date', $saleDate)
                    ->whereRaw('LOWER(buyer_name) = ?', [strtolower($buyerName)])
                    ->exists();
                if ($exists) {
                    $skipped++;
                    continue;
                }
            }

            try {
                $clientSale = $recorder->record([
                    'bird_batch_id' => $batchId,
                    'date' => $saleDate,
                    'buyer_name' => $buyerName,
                    'buyer_contact' => $clientData['buyer_contact'] ?? null,
                    'amount_paid' => $clientData['amount_paid'] ?? null,
                    'notes' => $clientData['notes'] ?? null,
                    'items' => $clientData['items'],
                ]);

                try {
                    $integrationService->pushEggClientSale($clientSale);
                } catch (\Exception $e) {
                    \Log::error('Bulk import: Priority Bank push failed', [
                        'egg_client_sale_id' => $clientSale->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                app(CrudNotificationService::class)->notify('egg_sales', 'created', $clientSale, auth()->user());
                $created++;
            } catch (\Throwable $e) {
                $errors[] = ($buyerName ?: 'Unknown client') . ': ' . $e->getMessage();
            }
        }

        if ($created === 0 && $skipped > 0 && empty($errors)) {
            return redirect()->route('egg-sales.bulk-import')
                ->withInput()
                ->with('info', "All {$skipped} client(s) already exist for this batch and date. Nothing imported.");
        }

        if ($created === 0) {
            $message = $errors[0] ?? 'Import failed. No records were created.';
            return redirect()->route('egg-sales.bulk-import')
                ->withInput()
                ->with('error', $message);
        }

        $summary = "Bulk import complete: {$created} client sale(s) added.";
        if ($skipped > 0) {
            $summary .= " {$skipped} duplicate(s) skipped.";
        }
        if (! empty($errors)) {
            $summary .= ' Some rows failed: ' . implode('; ', array_slice($errors, 0, 3));
        }

        return redirect()->route('egg-sales.index')->with('success', $summary);
    }
}
