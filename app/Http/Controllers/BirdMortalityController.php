<?php

namespace App\Http\Controllers;

use App\Models\BirdBatch;
use App\Models\BirdBatchRecord;
use Illuminate\Http\Request;

class BirdMortalityController extends Controller
{
    public function index(Request $request)
    {
        $query = BirdBatchRecord::with('birdBatch.house.farm')
            ->orderBy('record_date', 'desc');

        if ($request->filled('batch_id')) {
            $query->where('bird_batch_id', $request->batch_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('record_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('record_date', '<=', $request->date_to);
        }

        $records = $query->paginate(15)->withQueryString();
        $batches = BirdBatch::where('status', 'active')->orderBy('batch_code')->get();

        $totalMortality = BirdBatchRecord::sum('mortality_count');
        $totalCulled = BirdBatchRecord::sum('cull_count');

        return view('bird-mortality.index', compact('records', 'batches', 'totalMortality', 'totalCulled'));
    }

    public function create()
    {
        $batches = BirdBatch::where('status', 'active')
            ->with('house.farm')
            ->orderBy('batch_code')
            ->get();

        return view('bird-mortality.create', compact('batches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bird_batch_id' => 'required|exists:bird_batches,id',
            'record_date' => 'required|date',
            'mortality_count' => 'required|integer|min:0',
            'cull_count' => 'nullable|integer|min:0',
            'feed_used_kg' => 'nullable|numeric|min:0',
            'water_used_litres' => 'nullable|numeric|min:0',
            'average_weight_kg' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $data['cull_count'] = $data['cull_count'] ?? 0;
        $data['feed_used_kg'] = $data['feed_used_kg'] ?? 0;

        BirdBatchRecord::create($data);

        return redirect()->route('bird-mortality.index')
            ->with('success', 'Bird mortality record added successfully.');
    }

    public function edit(BirdBatchRecord $bird_mortality)
    {
        $batches = BirdBatch::with('house.farm')
            ->orderBy('batch_code')
            ->get();

        return view('bird-mortality.edit', compact('bird_mortality', 'batches'));
    }

    public function update(Request $request, BirdBatchRecord $bird_mortality)
    {
        $data = $request->validate([
            'bird_batch_id' => 'required|exists:bird_batches,id',
            'record_date' => 'required|date',
            'mortality_count' => 'required|integer|min:0',
            'cull_count' => 'nullable|integer|min:0',
            'feed_used_kg' => 'nullable|numeric|min:0',
            'water_used_litres' => 'nullable|numeric|min:0',
            'average_weight_kg' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $data['cull_count'] = $data['cull_count'] ?? 0;

        $bird_mortality->update($data);

        return redirect()->route('bird-mortality.index')
            ->with('success', 'Bird mortality record updated successfully.');
    }

    public function destroy(BirdBatchRecord $bird_mortality)
    {
        $bird_mortality->delete();

        return redirect()->route('bird-mortality.index')
            ->with('success', 'Bird mortality record deleted successfully.');
    }
}
