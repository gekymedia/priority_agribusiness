<?php

namespace App\Services;

use App\Models\BirdBatch;
use App\Models\MedicationCalendar;
use App\Models\MedicationSchedule;
use App\Models\Task;
use App\Services\BlackTaskSyncService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MedicationScheduleService
{
    /**
     * Assign a medication calendar to a bird batch and generate all scheduled tasks.
     */
    public function assignCalendarToBatch(BirdBatch $batch, MedicationCalendar $calendar): array
    {
        // Ensure relationships are loaded
        $batch->load(['farm', 'house']);
        
        $startDate = $batch->arrival_date ?? Carbon::now();
        $schedules = [];
        $tasks = [];

        // Ensure schedule is an array
        $schedule = is_array($calendar->schedule) ? $calendar->schedule : [];
        
        foreach ($schedule as $medication) {
            // Calculate the scheduled date based on week and day
            $scheduledDate = $startDate->copy()->addWeeks($medication['week'] - 1)->addDays($medication['day'] - 1);

            // Create medication schedule entry
            $schedule = MedicationSchedule::create([
                'bird_batch_id' => $batch->id,
                'medication_calendar_id' => $calendar->id,
                'start_date' => $startDate,
                'week_number' => $medication['week'],
                'medication_name' => $medication['medication_name'],
                'description' => $medication['description'] ?? null,
                'dosage' => $medication['dosage'] ?? null,
                'method' => $medication['method'] ?? null,
                'scheduled_date' => $scheduledDate,
                'is_completed' => false,
            ]);

            // Create task for this medication
            // Try to assign to employee if house has one assigned
            $assignedTo = null;
            if ($batch->house && $batch->house->employees()->where('is_active', true)->exists()) {
                $assignedTo = $batch->house->employees()->where('is_active', true)->first()->id;
            } elseif ($batch->farm && $batch->farm->employees()->where('is_active', true)->exists()) {
                $assignedTo = $batch->farm->employees()->where('is_active', true)->first()->id;
            }
            
            $task = Task::create([
                'related_type' => 'medication_schedule',
                'related_id' => $schedule->id,
                'title' => "Medication: {$medication['medication_name']} - Batch {$batch->batch_code}",
                'description' => $this->buildTaskDescription($medication, $batch, $scheduledDate),
                'due_date' => $scheduledDate,
                'priority' => $this->determinePriority($medication),
                'status' => 'pending',
                'created_by' => Auth::id(),
                'assigned_to' => $assignedTo,
            ]);

            // Link task to schedule
            $schedule->update(['task_id' => $task->id]);

            // Sync task to BlackTask if enabled
            if (config('services.blacktask.enabled')) {
                $syncService = app(BlackTaskSyncService::class);
                $syncService->syncTask($task);
            }

            $schedules[] = $schedule;
            $tasks[] = $task;
        }

        return [
            'schedules' => $schedules,
            'tasks' => $tasks,
        ];
    }

    /**
     * Build task description from medication details.
     */
    private function buildTaskDescription(array $medication, BirdBatch $batch, Carbon $scheduledDate): string
    {
        $description = "Medication Schedule for Batch: {$batch->batch_code}\n\n";
        $description .= "Medication: {$medication['medication_name']}\n";
        
        if (isset($medication['description'])) {
            $description .= "Description: {$medication['description']}\n";
        }
        
        if (isset($medication['dosage'])) {
            $description .= "Dosage: {$medication['dosage']}\n";
        }
        
        if (isset($medication['method'])) {
            $description .= "Method: {$medication['method']}\n";
        }
        
        $description .= "\nScheduled Date: {$scheduledDate->format('F d, Y')}\n";
        $farmName = $batch->farm ? $batch->farm->name : 'N/A';
        $description .= "Farm: {$farmName}\n";
        $houseName = $batch->house ? $batch->house->name : 'N/A';
        $description .= "House: {$houseName}\n";
        $description .= "Quantity: {$batch->quantity_arrived} birds";

        return $description;
    }

    /**
     * Determine task priority based on medication type.
     */
    private function determinePriority(array $medication): string
    {
        $name = strtolower($medication['medication_name']);
        
        // Critical vaccinations get high priority
        if (str_contains($name, 'marek') || str_contains($name, 'newcastle') || str_contains($name, 'gumboro')) {
            return 'high';
        }
        
        // Regular medications get medium priority
        if (str_contains($name, 'vaccine') || str_contains($name, 'prevention')) {
            return 'medium';
        }
        
        // Health checks get low priority
        return 'low';
    }

    /**
     * Mark a medication schedule as completed and update related task.
     */
    public function completeSchedule(MedicationSchedule $schedule): void
    {
        $schedule->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        if ($schedule->task) {
            $schedule->task->update([
                'status' => 'done',
                'completed_at' => now(),
            ]);
            
            // Sync task completion to BlackTask if enabled
            if (config('services.blacktask.enabled') && $schedule->task->blacktask_task_id) {
                $syncService = app(BlackTaskSyncService::class);
                $syncService->updateTask($schedule->task);
            }
        }
    }

    /**
     * Get upcoming medication schedules for a batch.
     */
    public function getUpcomingSchedules(BirdBatch $batch, int $days = 7): array
    {
        return MedicationSchedule::where('bird_batch_id', $batch->id)
            ->where('is_completed', false)
            ->where('scheduled_date', '>=', Carbon::now())
            ->where('scheduled_date', '<=', Carbon::now()->addDays($days))
            ->orderBy('scheduled_date')
            ->get()
            ->toArray();
    }
}

