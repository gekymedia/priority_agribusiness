<?php

namespace App\Http\Controllers;

use App\Models\BirdSale;
use App\Models\BirdBatch;
use App\Services\PriorityBankIntegrationService;
use App\Services\CrudNotificationService;
use Illuminate\Http\Request;

class BirdSaleController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->query('sort', 'date');
        $direction = strtolower($request->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['date', 'batch', 'farm', 'quantity_sold', 'price_per_bird', 'buyer_name'];
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'date';
        }

        $query = BirdSale::query()->with('birdBatch.farm');
        switch ($sort) {
            case 'date':
            case 'quantity_sold':
            case 'price_per_bird':
            case 'buyer_name':
                $query->orderBy($sort, $direction);
                break;
            case 'batch':
                $query->leftJoin('bird_batches', 'bird_sales.bird_batch_id', '=', 'bird_batches.id')
                    ->select('bird_sales.*')
                    ->orderBy('bird_batches.batch_code', $direction);
                break;
            case 'farm':
                $query->leftJoin('bird_batches as bb', 'bird_sales.bird_batch_id', '=', 'bb.id')
                    ->leftJoin('farms', 'bb.farm_id', '=', 'farms.id')
                    ->select('bird_sales.*')
                    ->orderBy('farms.name', $direction);
                break;
        }
        $sales = $query->paginate(50)->withQueryString();
        return view('bird-sales.index', compact('sales', 'sort', 'direction'));
    }

    public function create()
    {
        $batches = BirdBatch::with('farm')
            ->withSum('dailyRecords', 'mortality_count')
            ->withSum('dailyRecords', 'cull_count')
            ->withSum('birdSales', 'quantity_sold')
            ->get();
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

        app(CrudNotificationService::class)->notify('bird_sales', 'created', $birdSale, auth()->user());

        return redirect()->route('bird-sales.index')->with('success', 'Bird sale recorded successfully.');
    }

    public function show(BirdSale $birdSale)
    {
        $birdSale->load('birdBatch.farm');
        return view('bird-sales.show', compact('birdSale'));
    }

    public function edit(BirdSale $birdSale)
    {
        $batches = BirdBatch::with('farm')
            ->withSum('dailyRecords', 'mortality_count')
            ->withSum('dailyRecords', 'cull_count')
            ->withSum('birdSales', 'quantity_sold')
            ->get();
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

        app(CrudNotificationService::class)->notify('bird_sales', 'updated', $birdSale, auth()->user());

        return redirect()->route('bird-sales.index')->with('success', 'Bird sale updated successfully.');
    }

    public function destroy(BirdSale $birdSale)
    {
        $recordCopy = clone $birdSale;
        $birdSale->delete();

        app(CrudNotificationService::class)->notify('bird_sales', 'deleted', $recordCopy, auth()->user());

        return redirect()->route('bird-sales.index')->with('success', 'Bird sale deleted successfully.');
    }
}
