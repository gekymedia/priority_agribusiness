<?php

namespace App\Http\Controllers;

use App\Models\EggSale;
use App\Models\BirdBatch;
use App\Models\MarketOrder;
use App\Models\PaymentSetting;
use App\Services\PriorityBankIntegrationService;
use App\Services\CrudNotificationService;
use Illuminate\Http\Request;

class EggSaleController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->query('sort', 'date');
        $direction = strtolower($request->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['date', 'batch', 'farm', 'quantity_sold', 'price_per_unit', 'buyer_name'];
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'date';
        }

        $salesQuery = EggSale::query()->with('birdBatch.farm');
        switch ($sort) {
            case 'date':
                $salesQuery->orderBy('date', $direction);
                break;
            case 'quantity_sold':
            case 'price_per_unit':
            case 'buyer_name':
                $salesQuery->orderBy($sort, $direction);
                break;
            case 'batch':
                $salesQuery->leftJoin('bird_batches', 'egg_sales.bird_batch_id', '=', 'bird_batches.id')
                    ->select('egg_sales.*')
                    ->orderBy('bird_batches.batch_code', $direction);
                break;
            case 'farm':
                $salesQuery->leftJoin('bird_batches as bb', 'egg_sales.bird_batch_id', '=', 'bb.id')
                    ->leftJoin('farms', 'bb.farm_id', '=', 'farms.id')
                    ->select('egg_sales.*')
                    ->orderBy('farms.name', $direction);
                break;
        }
        $sales = $salesQuery->paginate(50)->withQueryString();

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

        return view('egg-sales.index', compact('sales', 'onlineOrders', 'sort', 'direction', 'onlineSort', 'onlineDir'));
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
                'buyer_name' => $order->customer_name,
                'buyer_contact' => $order->customer_phone,
                'notes' => 'Online order ' . $order->order_number,
            ]);
        }

        $order->update(['status' => MarketOrder::STATUS_DELIVERED]);

        try {
            $integrationService = new PriorityBankIntegrationService();
            /** @var \App\Models\EggSale $eggSale */
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
        return view('egg-sales.create', compact('batches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bird_batch_id' => 'required|exists:bird_batches,id',
            'date' => 'required|date',
            'quantity_sold' => 'required|integer|min:1',
            'unit_type' => 'required|string|in:tray,piece,crate',
            'price_per_unit' => 'required|numeric|min:0',
            'buyer_name' => 'nullable|string|max:255',
            'buyer_contact' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $eggSale = EggSale::create($data);

        // Push to Priority Bank
        try {
            $integrationService = new PriorityBankIntegrationService();
            $integrationService->pushEggSale($eggSale);
        } catch (\Exception $e) {
            \Log::error('Failed to push egg sale to Priority Bank', [
                'egg_sale_id' => $eggSale->id,
                'error' => $e->getMessage(),
            ]);
        }

        app(CrudNotificationService::class)->notify('egg_sales', 'created', $eggSale, auth()->user());

        return redirect()->route('egg-sales.index')->with('success', 'Egg sale recorded successfully.');
    }

    public function show(EggSale $eggSale)
    {
        $eggSale->load('birdBatch.farm');
        return view('egg-sales.show', compact('eggSale'));
    }

    public function edit(EggSale $eggSale)
    {
        $batches = BirdBatch::whereIn('purpose', ['egg_production', 'layer'])
            ->with('farm')
            ->get();
        return view('egg-sales.edit', compact('eggSale', 'batches'));
    }

    public function update(Request $request, EggSale $eggSale)
    {
        $data = $request->validate([
            'bird_batch_id' => 'required|exists:bird_batches,id',
            'date' => 'required|date',
            'quantity_sold' => 'required|integer|min:1',
            'unit_type' => 'required|string|in:tray,piece,crate',
            'price_per_unit' => 'required|numeric|min:0',
            'buyer_name' => 'nullable|string|max:255',
            'buyer_contact' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $eggSale->update($data);

        app(CrudNotificationService::class)->notify('egg_sales', 'updated', $eggSale, auth()->user());

        return redirect()->route('egg-sales.index')->with('success', 'Egg sale updated successfully.');
    }

    public function destroy(EggSale $eggSale)
    {
        $recordCopy = clone $eggSale;
        $eggSale->delete();

        app(CrudNotificationService::class)->notify('egg_sales', 'deleted', $recordCopy, auth()->user());

        return redirect()->route('egg-sales.index')->with('success', 'Egg sale deleted successfully.');
    }
}
