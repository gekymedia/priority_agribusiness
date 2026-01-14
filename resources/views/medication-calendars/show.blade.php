@extends('layouts.app')

@section('title', 'Medication Calendar Details')

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ $medicationCalendar->name }}</h1>
    <p class="page-subtitle">{{ $medicationCalendar->description ?? 'Medication and vaccination schedule' }}</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('medication-calendars.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Calendars
        </a>
    </div>
    <div>
        <span class="badge bg-primary me-2">{{ ucfirst($medicationCalendar->type) }}</span>
        @if($medicationCalendar->is_default)
            <span class="badge bg-success">Default Calendar</span>
        @endif
    </div>
</div>

<div class="agri-card mb-4">
    <div class="agri-card-header">
        <h3><i class="fas fa-info-circle me-2"></i>Calendar Information</h3>
    </div>
    <div class="agri-card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <strong>Type:</strong> <span class="badge bg-primary">{{ ucfirst($medicationCalendar->type) }}</span>
            </div>
            <div class="col-md-6">
                <strong>Total Medications:</strong> <span class="badge bg-success">{{ count($medicationCalendar->schedule ?? []) }}</span>
            </div>
            <div class="col-md-6">
                <strong>Duration:</strong>
                @if($medicationCalendar->schedule && count($medicationCalendar->schedule) > 0)
                    @php
                        $maxWeek = max(array_column($medicationCalendar->schedule, 'week'));
                    @endphp
                    <span>{{ $maxWeek }} weeks</span>
                @else
                    <span>N/A</span>
                @endif
            </div>
            <div class="col-md-6">
                <strong>Status:</strong>
                @if($medicationCalendar->is_active)
                    <span class="badge bg-success">Active</span>
                @else
                    <span class="badge bg-secondary">Inactive</span>
                @endif
            </div>
            @if($medicationCalendar->description)
                <div class="col-12">
                    <strong>Description:</strong>
                    <p class="mb-0">{{ $medicationCalendar->description }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="agri-card">
    <div class="agri-card-header">
        <h3><i class="fas fa-list me-2"></i>Medication Schedule</h3>
    </div>
    <div class="agri-card-body">
        @if($medicationCalendar->schedule && count($medicationCalendar->schedule) > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Week</th>
                            <th>Day</th>
                            <th>Medication Name</th>
                            <th>Description</th>
                            <th>Dosage</th>
                            <th>Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medicationCalendar->schedule as $medication)
                            <tr>
                                <td><span class="badge bg-primary">Week {{ $medication['week'] }}</span></td>
                                <td><span class="badge bg-info">Day {{ $medication['day'] }}</span></td>
                                <td><strong>{{ $medication['medication_name'] }}</strong></td>
                                <td>{{ $medication['description'] ?? 'N/A' }}</td>
                                <td>{{ $medication['dosage'] ?? 'As per manufacturer' }}</td>
                                <td><span class="badge bg-secondary">{{ $medication['method'] ?? 'N/A' }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">No medications scheduled in this calendar</p>
            </div>
        @endif
    </div>
</div>

<div class="alert alert-modern alert-info mt-4">
    <i class="fas fa-info-circle me-2"></i>
    <strong>How to Use:</strong> When creating a new bird batch, you can select this medication calendar. 
    The system will automatically create tasks for each scheduled medication based on the batch arrival date. 
    Tasks will appear in your task list with appropriate due dates and priorities.
</div>
@endsection

