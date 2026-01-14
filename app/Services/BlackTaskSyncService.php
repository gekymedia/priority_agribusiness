<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BlackTaskSyncService
{
    /**
     * Sync a task to BlackTask
     */
    public function syncTask(Task $task): bool
    {
        $apiUrl = config('services.blacktask.api_url');
        $apiKey = config('services.blacktask.api_key');

        if (!$apiUrl || !$apiKey) {
            Log::warning('BlackTask API not configured. Skipping task sync.', [
                'task_id' => $task->id
            ]);
            return false;
        }

        try {
            // Map priority from agribusiness to blacktask
            $priorityMap = [
                'low' => 0,
                'medium' => 1,
                'high' => 2,
            ];
            $blacktaskPriority = $priorityMap[strtolower($task->priority)] ?? 1;

            // Prepare task data for blacktask
            $description = $task->description ?? '';
            
            // Add employee assignment info to description if assigned
            if ($task->assignedEmployee) {
                $description .= ($description ? "\n\n" : '') . "Assigned to: {$task->assignedEmployee->full_name} ({$task->assignedEmployee->employee_id})";
            }
            
            $taskData = [
                'title' => $task->title,
                'task_date' => $task->due_date ? $task->due_date->format('Y-m-d') : now()->format('Y-m-d'),
                'priority' => $blacktaskPriority,
            ];
            
            // Add description if available
            if ($description) {
                $taskData['description'] = $description;
            }

            // Add reminder if due date is in the future
            if ($task->due_date && $task->due_date->isFuture()) {
                // Set reminder 1 day before due date
                $reminderDate = $task->due_date->copy()->subDay();
                if ($reminderDate->isFuture()) {
                    $taskData['reminder_at'] = $reminderDate->format('Y-m-d H:i:s');
                }
            }

            // Make API request to blacktask
            $response = Http::withToken($apiKey)
                ->post("{$apiUrl}/api/tasks", $taskData);

            if ($response->successful()) {
                // Store the blacktask task ID for future reference
                $blacktaskTaskId = $response->json('task.id');
                
                // Store blacktask_task_id in the task record
                if ($blacktaskTaskId) {
                    $task->update(['blacktask_task_id' => $blacktaskTaskId]);
                }
                
                Log::info('Task synced to BlackTask successfully', [
                    'agribusiness_task_id' => $task->id,
                    'blacktask_task_id' => $blacktaskTaskId,
                ]);

                return true;
            } else {
                Log::error('Failed to sync task to BlackTask', [
                    'task_id' => $task->id,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);

                return false;
            }
        } catch (\Exception $e) {
            Log::error('Exception while syncing task to BlackTask', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Update a task in BlackTask
     */
    public function updateTask(Task $task): bool
    {
        $apiUrl = config('services.blacktask.api_url');
        $apiKey = config('services.blacktask.api_key');

        if (!$apiUrl || !$apiKey || !$task->blacktask_task_id) {
            return false;
        }

        try {
            $priorityMap = [
                'low' => 0,
                'medium' => 1,
                'high' => 2,
            ];
            $blacktaskPriority = $priorityMap[strtolower($task->priority)] ?? 1;

            $description = $task->description ?? '';
            
            // Add employee assignment info to description if assigned
            if ($task->assignedEmployee) {
                $description .= ($description ? "\n\n" : '') . "Assigned to: {$task->assignedEmployee->full_name} ({$task->assignedEmployee->employee_id})";
            }
            
            $taskData = [
                'title' => $task->title,
                'task_date' => $task->due_date ? $task->due_date->format('Y-m-d') : now()->format('Y-m-d'),
                'priority' => $blacktaskPriority,
            ];
            
            // Add description if available
            if ($description) {
                $taskData['description'] = $description;
            }

            // If task is completed, mark it as done in blacktask
            if ($task->status === 'done') {
                $taskData['is_done'] = true;
            }

            $response = Http::withToken($apiKey)
                ->patch("{$apiUrl}/api/tasks/{$task->blacktask_task_id}", $taskData);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Exception while updating task in BlackTask', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Delete a task from BlackTask
     */
    public function deleteTask(Task $task): bool
    {
        $apiUrl = config('services.blacktask.api_url');
        $apiKey = config('services.blacktask.api_key');

        if (!$apiUrl || !$apiKey || !$task->blacktask_task_id) {
            return false;
        }

        try {
            $response = Http::withToken($apiKey)
                ->delete("{$apiUrl}/api/tasks/{$task->blacktask_task_id}");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Exception while deleting task from BlackTask', [
                'task_id' => $task->id,
                'blacktask_task_id' => $task->blacktask_task_id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}

