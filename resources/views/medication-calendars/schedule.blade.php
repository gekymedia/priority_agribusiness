@extends('layouts.app')

@section('title', 'Medication Schedule')

@section('content')
<div class="page-header">
    <h1 class="page-title">Medication Schedule</h1>
    <p class="page-subtitle">Batch: {{ $batch->batch_code }}</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('batches.show', $batch) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Batch
        </a>
    </div>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        @if($schedules->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Week</th>
                            <th>Scheduled Date</th>
                            <th>Medication</th>
                            <th>Description</th>
                            <th>Dosage</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Task</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedules as $schedule)
                            <tr>
                                <td><span class="badge bg-primary">Week {{ $schedule->week_number }}</span></td>
                                <td>
                                    <strong>{{ $schedule->scheduled_date->format('M d, Y') }}</strong>
                                    @if($schedule->scheduled_date->isPast() && !$schedule->is_completed)
                                        <span class="badge bg-danger ms-2">Overdue</span>
                                    @elseif($schedule->scheduled_date->isToday())
                                        <span class="badge bg-warning ms-2">Today</span>
                                    @elseif($schedule->scheduled_date->isFuture() && $schedule->scheduled_date->diffInDays(now()) <= 7)
                                        <span class="badge bg-info ms-2">Upcoming</span>
                                    @endif
                                </td>
                                <td><strong>{{ $schedule->medication_name }}</strong></td>
                                <td>{{ $schedule->description ?? 'N/A' }}</td>
                                <td>{{ $schedule->dosage ?? 'N/A' }}</td>
                                <td><span class="badge bg-secondary">{{ $schedule->method ?? 'N/A' }}</span></td>
                                <td>
                                    @if($schedule->is_completed)
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if($schedule->task)
                                        <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-tasks"></i> View Task
                                        </a>
                                    @else
                                        <span class="text-muted">No task</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$schedule->is_completed)
                                        <form action="{{ route('medication-schedules.complete', $schedule) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Mark this medication as completed?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i> Complete
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted">
                                            Completed: {{ $schedule->completed_at->format('M d, Y') }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-icon bg-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-value">{{ $schedules->where('is_completed', true)->count() }}</div>
                            <div class="stat-label">Completed</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-icon bg-warning">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-value">{{ $schedules->where('is_completed', false)->count() }}</div>
                            <div class="stat-label">Pending</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-icon bg-info">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div class="stat-value">{{ $schedules->where('scheduled_date', '>=', now())->where('scheduled_date', '<=', now()->addDays(7))->count() }}</div>
                            <div class="stat-label">Upcoming (7 days)</div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <p class="text-muted">No medication schedule found for this batch.</p>
                <a href="{{ route('batches.assign-medication', $batch) }}" class="btn btn-agri">
                    <i class="fas fa-plus me-2"></i>Assign Medication Calendar
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

