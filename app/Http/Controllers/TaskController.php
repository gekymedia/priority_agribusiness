<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\BlackTaskSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks.
     */
    public function index(Request $request)
    {
        $query = Task::with(['creator', 'assignedEmployee']);
        
        // Filter by assigned employee if requested
        if ($request->has('assigned_to') && $request->assigned_to) {
            $query->where('assigned_to', $request->assigned_to);
        }
        
        // If logged in as employee, show only their tasks
        $user = Auth::user();
        if ($user instanceof \App\Models\Employee) {
            $query->where('assigned_to', $user->id);
        }
        
        $tasks = $query->orderBy('due_date')->paginate(15);
        $employees = \App\Models\Employee::where('is_active', true)->orderBy('first_name')->get();
        
        return view('tasks.index', compact('tasks', 'employees'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        $employees = \App\Models\Employee::where('is_active', true)
            ->orderBy('first_name')
            ->get();
        return view('tasks.create', compact('employees'));
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request, BlackTaskSyncService $syncService)
    {
        $data = $request->validate([
            'related_type' => 'nullable|string',
            'related_id' => 'nullable|integer',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|string|in:low,medium,high',
            'assigned_to' => 'nullable|exists:employees,id',
        ]);
        $data['status'] = 'pending';
        $data['created_by'] = Auth::id();
        
        $task = Task::create($data);
        
        // Sync to BlackTask if enabled
        if (config('services.blacktask.enabled')) {
            $syncService->syncTask($task);
        }
        
        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        $employees = \App\Models\Employee::where('is_active', true)
            ->orderBy('first_name')
            ->get();
        return view('tasks.edit', compact('task', 'employees'));
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Task $task, BlackTaskSyncService $syncService)
    {
        $data = $request->validate([
            'related_type' => 'nullable|string',
            'related_id' => 'nullable|integer',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|string|in:low,medium,high',
            'status' => 'required|string|in:pending,in_progress,done',
            'assigned_to' => 'nullable|exists:employees,id',
        ]);
        
        $task->update($data);
        
        // Sync update to BlackTask if enabled and task was previously synced
        if (config('services.blacktask.enabled') && $task->blacktask_task_id) {
            $syncService->updateTask($task);
        }
        
        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task, BlackTaskSyncService $syncService)
    {
        // Delete from BlackTask first if synced
        if (config('services.blacktask.enabled') && $task->blacktask_task_id) {
            $syncService->deleteTask($task);
        }
        
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    /**
     * Mark the task as done.
     */
    public function complete(Task $task, BlackTaskSyncService $syncService)
    {
        $task->update([
            'status' => 'done',
            'completed_at' => now(),
        ]);
        
        // Sync completion to BlackTask if enabled and task was previously synced
        if (config('services.blacktask.enabled') && $task->blacktask_task_id) {
            $syncService->updateTask($task);
        }
        
        return redirect()->route('tasks.index')->with('success', 'Task marked as completed.');
    }
}