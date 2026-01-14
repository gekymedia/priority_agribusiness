@extends('layouts.app')

@section('title', 'Employee Details')

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ $employee->full_name }}</h1>
    <p class="page-subtitle">Employee Information and Details</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('employees.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Employees
        </a>
    </div>
    <div>
        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="agri-card mb-4">
            <div class="agri-card-body">
                <h5 class="mb-4"><i class="fas fa-user me-2"></i>Personal Information</h5>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Employee ID:</strong></div>
                    <div class="col-md-8">{{ $employee->employee_id }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Full Name:</strong></div>
                    <div class="col-md-8">{{ $employee->full_name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Email:</strong></div>
                    <div class="col-md-8">{{ $employee->email }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Phone:</strong></div>
                    <div class="col-md-8">{{ $employee->phone ?? 'N/A' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Hire Date:</strong></div>
                    <div class="col-md-8">{{ $employee->hire_date ? $employee->hire_date->format('F d, Y') : 'N/A' }}</div>
                </div>
                @if($employee->address)
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Address:</strong></div>
                    <div class="col-md-8">{{ $employee->address }}</div>
                </div>
                @endif
            </div>
        </div>

        <div class="agri-card mb-4">
            <div class="agri-card-body">
                <h5 class="mb-4"><i class="fas fa-briefcase me-2"></i>Assignment & Access</h5>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Access Level:</strong></div>
                    <div class="col-md-8">
                        @php
                            $badgeColors = [
                                'admin' => 'danger',
                                'manager' => 'warning',
                                'caretaker' => 'primary',
                                'viewer' => 'secondary'
                            ];
                        @endphp
                        <span class="badge bg-{{ $badgeColors[$employee->access_level] ?? 'secondary' }}">
                            {{ ucfirst($employee->access_level) }}
                        </span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Status:</strong></div>
                    <div class="col-md-8">
                        @if($employee->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
                @if($employee->farm)
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Assigned Farm:</strong></div>
                    <div class="col-md-8">
                        <a href="{{ route('farms.show', $employee->farm) }}" class="text-decoration-none">
                            <span class="badge bg-info">{{ $employee->farm->name }}</span>
                        </a>
                    </div>
                </div>
                @endif
                @if($employee->house)
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Assigned House:</strong></div>
                    <div class="col-md-8">
                        <a href="{{ route('houses.show', $employee->house) }}" class="text-decoration-none">
                            <span class="badge bg-info">{{ $employee->house->name }}</span>
                        </a>
                        <small class="text-muted ms-2">({{ $employee->house->farm->name }})</small>
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if($employee->notes)
        <div class="agri-card mb-4">
            <div class="agri-card-body">
                <h5 class="mb-4"><i class="fas fa-sticky-note me-2"></i>Notes</h5>
                <p class="text-muted">{{ $employee->notes }}</p>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="agri-card mb-4">
            <div class="agri-card-body">
                <h5 class="mb-4"><i class="fas fa-tasks me-2"></i>Assigned Tasks</h5>
                @if($employee->tasks && $employee->tasks->count() > 0)
                    <p class="text-muted mb-3">Total Tasks: <strong>{{ $employee->tasks->count() }}</strong></p>
                    <div class="list-group">
                        @foreach($employee->tasks->take(5) as $task)
                        <a href="{{ route('tasks.edit', $task) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $task->title }}</h6>
                                <small>
                                    @if($task->status === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </small>
                            </div>
                            @if($task->due_date)
                            <small class="text-muted">Due: {{ $task->due_date->format('M d, Y') }}</small>
                            @endif
                        </a>
                        @endforeach
                    </div>
                    @if($employee->tasks->count() > 5)
                    <a href="{{ route('tasks.index') }}?assigned_to={{ $employee->id }}" class="btn btn-sm btn-outline-primary mt-3">
                        View All Tasks
                    </a>
                    @endif
                @else
                    <p class="text-muted">No tasks assigned yet</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

