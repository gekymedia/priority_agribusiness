<?php

namespace App\Http\Controllers;

use App\Models\EggProduction;
use App\Models\BirdBatch;
use App\Services\CrudNotificationService;
use Illuminate\Http\Request;

class EggProductionController extends Controller
{
    public function index()
    {
        $productions = EggProduction::with('birdBatch.farm')->latest('date')->paginate(15);
        return view('egg-productions.index', compact('productions'));
    }

    public function create()
    {
        $batches = BirdBatch::whereIn('purpose', ['egg_production', 'layer'])
            ->with('farm')
            ->get();
        return view('egg-productions.create', compact('batches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bird_batch_id' => 'required|exists:bird_batches,id',
            'date' => 'required|date',
            'eggs_collected' => 'required|integer|min:0',
            'cracked_or_damaged' => 'required|integer|min:0',
            'eggs_used_internal' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $record = EggProduction::create($data);

        app(CrudNotificationService::class)->notify('egg_production', 'created', $record, auth()->user());

        return redirect()->route('egg-productions.index')->with('success', 'Egg production record created successfully.');
    }

    public function show(EggProduction $eggProduction)
    {
        $eggProduction->load('birdBatch.farm');
        return view('egg-productions.show', compact('eggProduction'));
    }

    public function edit(EggProduction $eggProduction)
    {
        $batches = BirdBatch::whereIn('purpose', ['egg_production', 'layer'])
            ->with('farm')
            ->get();
        return view('egg-productions.edit', compact('eggProduction', 'batches'));
    }

    public function update(Request $request, EggProduction $eggProduction)
    {
        $data = $request->validate([
            'bird_batch_id' => 'required|exists:bird_batches,id',
            'date' => 'required|date',
            'eggs_collected' => 'required|integer|min:0',
            'cracked_or_damaged' => 'required|integer|min:0',
            'eggs_used_internal' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $eggProduction->update($data);

        app(CrudNotificationService::class)->notify('egg_production', 'updated', $eggProduction, auth()->user());

        return redirect()->route('egg-productions.index')->with('success', 'Egg production record updated successfully.');
    }

    public function destroy(EggProduction $eggProduction)
    {
        $recordCopy = clone $eggProduction;
        $eggProduction->delete();

        app(CrudNotificationService::class)->notify('egg_production', 'deleted', $recordCopy, auth()->user());

        return redirect()->route('egg-productions.index')->with('success', 'Egg production record deleted successfully.');
    }
}
