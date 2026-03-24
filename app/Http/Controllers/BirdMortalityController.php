<?php

namespace App\Http\Controllers;

use App\Models\BirdBatch;
use App\Models\BirdBatchRecord;
use Illuminate\Http\Request;

class BirdMortalityController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->query('sort', 'record_date');
        $direction = strtolower($request->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['record_date', 'batch', 'house', 'mortality_count', 'cull_count', 'feed_used_kg', 'water_used_litres', 'average_weight_kg'];
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'record_date';
        }

        $query = BirdBatchRecord::query()->with('birdBatch.house.farm');

        if ($request->filled('batch_id')) {
            $query->where('bird_batch_id', $request->batch_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('record_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('record_date', '<=', $request->date_to);
        }

        switch ($sort) {
            case 'record_date':
            case 'mortality_count':
            case 'cull_count':
            case 'feed_used_kg':
            case 'water_used_litres':
            case 'average_weight_kg':
                $query->orderBy('bird_batch_records.' . $sort, $direction);
                break;
            case 'batch':
                $query->leftJoin('bird_batches', 'bird_batch_records.bird_batch_id', '=', 'bird_batches.id')
                    ->select('bird_batch_records.*')
                    ->orderBy('bird_batches.batch_code', $direction);
                break;
            case 'house':
                $query->leftJoin('bird_batches as bb', 'bird_batch_records.bird_batch_id', '=', 'bb.id')
                    ->leftJoin('houses', 'bb.house_id', '=', 'houses.id')
                    ->select('bird_batch_records.*')
                    ->orderBy('houses.name', $direction);
                break;
        }

        $records = $query->paginate(50)->withQueryString();
        $batches = BirdBatch::where('status', 'active')->orderBy('batch_code')->get();

        $totalMortality = BirdBatchRecord::sum('mortality_count');
        $totalCulled = BirdBatchRecord::sum('cull_count');

        return view('bird-mortality.index', compact('records', 'batches', 'totalMortality', 'totalCulled', 'sort', 'direction'));
    }

    public function create()
    {
        $batches = BirdBatch::where('status', 'active')
            ->with('house.farm')
            ->withSum('dailyRecords', 'mortality_count')
            ->withSum('dailyRecords', 'cull_count')
            ->withSum('birdSales', 'quantity_sold')
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
            ->withSum('dailyRecords', 'mortality_count')
            ->withSum('dailyRecords', 'cull_count')
            ->withSum('birdSales', 'quantity_sold')
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
