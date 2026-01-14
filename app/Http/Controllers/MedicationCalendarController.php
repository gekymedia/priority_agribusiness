<?php

namespace App\Http\Controllers;

use App\Models\BirdBatch;
use App\Models\MedicationCalendar;
use App\Services\MedicationScheduleService;
use Illuminate\Http\Request;

class MedicationCalendarController extends Controller
{
    protected $scheduleService;

    public function __construct(MedicationScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * Display available medication calendars for selection.
     */
    public function index()
    {
        $calendars = MedicationCalendar::where('is_active', true)->get();
        return view('medication-calendars.index', compact('calendars'));
    }

    /**
     * Show details of a specific medication calendar.
     */
    public function show(MedicationCalendar $medicationCalendar)
    {
        return view('medication-calendars.show', compact('medicationCalendar'));
    }

    /**
     * Show form to assign calendar to a batch.
     */
    public function assignForm(BirdBatch $batch)
    {
        $calendars = MedicationCalendar::where('is_active', true)->get();
        return view('medication-calendars.assign', compact('batch', 'calendars'));
    }

    /**
     * Assign medication calendar to a batch and generate tasks.
     */
    public function assign(Request $request, BirdBatch $batch)
    {
        $request->validate([
            'medication_calendar_id' => 'required|exists:medication_calendars,id',
        ]);

        $calendar = MedicationCalendar::findOrFail($request->medication_calendar_id);

        // Check if batch already has a calendar assigned
        if ($batch->medicationSchedules()->exists()) {
            return redirect()
                ->route('batches.show', $batch)
                ->with('error', 'This batch already has a medication schedule assigned.');
        }

        // Assign calendar and generate tasks
        $result = $this->scheduleService->assignCalendarToBatch($batch, $calendar);

        return redirect()
            ->route('batches.show', $batch)
            ->with('success', "Medication calendar assigned successfully! " . count($result['tasks']) . " tasks have been created.");
    }

    /**
     * View medication schedule for a batch.
     */
    public function viewSchedule(BirdBatch $batch)
    {
        $schedules = $batch->medicationSchedules()
            ->with('task')
            ->orderBy('scheduled_date')
            ->get();

        return view('medication-calendars.schedule', compact('batch', 'schedules'));
    }

    /**
     * Mark medication as completed.
     */
    public function completeSchedule(Request $request, $scheduleId)
    {
        $schedule = \App\Models\MedicationSchedule::findOrFail($scheduleId);
        
        $this->scheduleService->completeSchedule($schedule);

        return redirect()
            ->back()
            ->with('success', 'Medication marked as completed.');
    }
}
