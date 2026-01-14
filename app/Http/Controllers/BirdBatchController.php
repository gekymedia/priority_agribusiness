<?php

namespace App\Http\Controllers;

use App\Models\BirdBatch;
use App\Models\Farm;
use App\Models\House;
use App\Models\MedicationCalendar;
use App\Services\MedicationScheduleService;
use Illuminate\Http\Request;

class BirdBatchController extends Controller
{
    public function index()
    {
        $batches = BirdBatch::with(['farm', 'house'])->paginate(15);
        return view('batches.index', compact('batches'));
    }

    public function create()
    {
        $farms = Farm::all();
        $houses = House::all();
        $medicationCalendars = MedicationCalendar::where('is_active', true)->get();
        return view('batches.create', compact('farms', 'houses', 'medicationCalendars'));
    }

    public function store(Request $request, MedicationScheduleService $scheduleService)
    {
        $data = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'house_id' => 'required|exists:houses,id',
            'batch_code' => 'required|string|max:255',
            'breed' => 'nullable|string|max:255',
            'purpose' => 'required|string',
            'arrival_date' => 'required|date',
            'quantity_arrived' => 'required|integer',
            'cost_per_bird' => 'nullable|numeric',
            'supplier_name' => 'nullable|string|max:255',
            'status' => 'required|string',
            'medication_calendar_id' => 'nullable|exists:medication_calendars,id',
        ]);
        
        $batch = BirdBatch::create($data);
        
        // Assign medication calendar if selected
        if ($request->filled('medication_calendar_id')) {
            $calendar = MedicationCalendar::findOrFail($request->medication_calendar_id);
            $result = $scheduleService->assignCalendarToBatch($batch, $calendar);
            $taskCount = count($result['tasks']);
            return redirect()->route('batches.index')->with('success', "Bird batch created successfully! Medication calendar assigned and {$taskCount} tasks have been created.");
        }
        
        return redirect()->route('batches.index')->with('success', 'Bird batch created successfully.');
    }

    public function show(BirdBatch $batch)
    {
        return view('batches.show', ['batch' => $batch]);
    }

    public function edit(BirdBatch $batch)
    {
        $farms = Farm::all();
        $houses = House::all();
        return view('batches.edit', compact('batch', 'farms', 'houses'));
    }

    public function update(Request $request, BirdBatch $batch)
    {
        $data = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'house_id' => 'required|exists:houses,id',
            'batch_code' => 'required|string|max:255',
            'breed' => 'nullable|string|max:255',
            'purpose' => 'required|string',
            'arrival_date' => 'required|date',
            'quantity_arrived' => 'required|integer',
            'cost_per_bird' => 'nullable|numeric',
            'supplier_name' => 'nullable|string|max:255',
            'status' => 'required|string',
        ]);
        $batch->update($data);
        return redirect()->route('batches.index')->with('success', 'Bird batch updated successfully.');
    }

    public function destroy(BirdBatch $batch)
    {
        $batch->delete();
        return redirect()->route('batches.index')->with('success', 'Bird batch deleted successfully.');
    }
}