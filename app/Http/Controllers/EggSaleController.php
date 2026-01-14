<?php

namespace App\Http\Controllers;

use App\Models\EggSale;
use App\Models\BirdBatch;
use App\Services\PriorityBankIntegrationService;
use Illuminate\Http\Request;

class EggSaleController extends Controller
{
    public function index()
    {
        $sales = EggSale::with('birdBatch.farm')->latest('date')->paginate(15);
        return view('egg-sales.index', compact('sales'));
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

        return redirect()->route('egg-sales.index')->with('success', 'Egg sale updated successfully.');
    }

    public function destroy(EggSale $eggSale)
    {
        $eggSale->delete();
        return redirect()->route('egg-sales.index')->with('success', 'Egg sale deleted successfully.');
    }
}
