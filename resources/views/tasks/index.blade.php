@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
<div class="page-header">
    <h1 class="page-title">Tasks & Reminders</h1>
    <p class="page-subtitle">Manage your agricultural tasks and reminders</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('tasks.create') }}" class="btn btn-agri">
            <i class="fas fa-plus me-2"></i>Add Task
        </a>
    </div>
    @if(isset($employees) && $employees->count() > 0)
    <div>
        <form method="GET" action="{{ route('tasks.index') }}" class="d-inline">
            <select name="assigned_to" class="form-select form-select-sm d-inline-block" style="width: auto;" onchange="this.form.submit()">
                <option value="">All Tasks</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ request('assigned_to') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->full_name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
    @endif
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <div class="table-responsive">
            <table class="table table-hover">
    <thead>
        <tr>
            <th>Title</th>
            <th>Assigned To</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
                    @forelse($tasks as $task)
        <tr>
                        <td><strong>{{ $task->title }}</strong></td>
                        <td>
                            @if($task->assignedEmployee)
                                <span class="badge bg-info">
                                    <i class="fas fa-user me-1"></i>{{ $task->assignedEmployee->full_name }}
                                </span>
                            @else
                                <span class="text-muted">Unassigned</span>
                            @endif
                        </td>
                        <td>{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, Y') : '-' }}</td>
                        <td>
                            @if($task->status == 'done')
                                <span class="badge bg-success">{{ ucfirst($task->status) }}</span>
                            @elseif($task->status == 'pending')
                                <span class="badge bg-warning">{{ ucfirst($task->status) }}</span>
                            @else
                                <span class="badge bg-info">{{ ucfirst($task->status) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($task->priority == 'high')
                                <span class="badge bg-danger">{{ ucfirst($task->priority) }}</span>
                            @elseif($task->priority == 'medium')
                                <span class="badge bg-warning">{{ ucfirst($task->priority) }}</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($task->priority) }}</span>
                            @endif
                        </td>
            <td>
                @if($task->status !== 'done')
                                <form action="{{ route('tasks.complete', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('Mark this task as done?');">
                        @csrf
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i>
                                    </button>
                    </form>
                @endif
                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this task?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No tasks created yet</p>
                            <a href="{{ route('tasks.create') }}" class="btn btn-agri">
                                <i class="fas fa-plus me-2"></i>Add First Task
                            </a>
            </td>
        </tr>
                    @endforelse
    </tbody>
</table>
        </div>

        @if($tasks->hasPages())
        <div class="mt-4">
            {{ $tasks->links() }}
        </div>
        @endif
    </div>
</div>
@endsection