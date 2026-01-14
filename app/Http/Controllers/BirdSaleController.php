<?php

namespace App\Http\Controllers;

use App\Models\BirdSale;
use App\Models\BirdBatch;
use App\Services\PriorityBankIntegrationService;
use Illuminate\Http\Request;

class BirdSaleController extends Controller
{
    public function index()
    {
        $sales = BirdSale::with('birdBatch.farm')->latest('date')->paginate(15);
        return view('bird-sales.index', compact('sales'));
    }

    public function create()
    {
        $batches = BirdBatch::with('farm')->get();
        return view('bird-sales.create', compact('batches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bird_batch_id' => 'required|exists:bird_batches,id',
            'date' => 'required|date',
            'quantity_sold' => 'required|integer|min:1',
            'price_per_bird' => 'required|numeric|min:0',
            'buyer_name' => 'nullable|string|max:255',
            'buyer_contact' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $birdSale = BirdSale::create($data);

        // Push to Priority Bank
        try {
            $integrationService = new PriorityBankIntegrationService();
            $integrationService->pushBirdSale($birdSale);
        } catch (\Exception $e) {
            \Log::error('Failed to push bird sale to Priority Bank', [
                'bird_sale_id' => $birdSale->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('bird-sales.index')->with('success', 'Bird sale recorded successfully.');
    }

    public function show(BirdSale $birdSale)
    {
        $birdSale->load('birdBatch.farm');
        return view('bird-sales.show', compact('birdSale'));
    }

    public function edit(BirdSale $birdSale)
    {
        $batches = BirdBatch::with('farm')->get();
        return view('bird-sales.edit', compact('birdSale', 'batches'));
    }

    public function update(Request $request, BirdSale $birdSale)
    {
        $data = $request->validate([
            'bird_batch_id' => 'required|exists:bird_batches,id',
            'date' => 'required|date',
            'quantity_sold' => 'required|integer|min:1',
            'price_per_bird' => 'required|numeric|min:0',
            'buyer_name' => 'nullable|string|max:255',
            'buyer_contact' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $birdSale->update($data);

        return redirect()->route('bird-sales.index')->with('success', 'Bird sale updated successfully.');
    }

    public function destroy(BirdSale $birdSale)
    {
        $birdSale->delete();
        return redirect()->route('bird-sales.index')->with('success', 'Bird sale deleted successfully.');
    }
}
