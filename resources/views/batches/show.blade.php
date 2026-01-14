@extends('layouts.app')

@section('title', 'View Bird Batch')

@section('content')
<div class="page-header">
    <h1 class="page-title">Batch: {{ $batch->batch_code }}</h1>
    <p class="page-subtitle">Bird batch details and medication schedule</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('batches.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>
    <div>
        <a href="{{ route('batches.edit', $batch) }}" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
        <form action="{{ route('batches.destroy', $batch) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this batch?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash me-2"></i>Delete
            </button>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="agri-card mb-4">
            <div class="agri-card-header">
                <h3><i class="fas fa-info-circle me-2"></i>Batch Information</h3>
            </div>
            <div class="agri-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <strong>Farm:</strong> {{ $batch->farm->name ?? 'N/A' }}
                    </div>
                    <div class="col-md-6">
                        <strong>House:</strong> {{ $batch->house->name ?? 'N/A' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Purpose:</strong> <span class="badge bg-primary">{{ ucfirst($batch->purpose) }}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong> 
                        @if($batch->status == 'active')
                            <span class="badge bg-success">{{ ucfirst($batch->status) }}</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($batch->status) }}</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>Arrival Date:</strong> {{ \Carbon\Carbon::parse($batch->arrival_date)->format('M d, Y') }}
                    </div>
                    <div class="col-md-6">
                        <strong>Quantity Arrived:</strong> <span class="badge bg-success">{{ number_format($batch->quantity_arrived) }}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Breed:</strong> {{ $batch->breed ?? 'N/A' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Cost per Bird:</strong> ₵{{ number_format($batch->cost_per_bird ?? 0, 2) }}
                    </div>
                    <div class="col-12">
                        <strong>Supplier:</strong> {{ $batch->supplier_name ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>

        @if($batch->medicationSchedules()->exists())
            <div class="agri-card">
                <div class="agri-card-header">
                    <h3><i class="fas fa-calendar-alt me-2"></i>Medication Schedule</h3>
                </div>
                <div class="agri-card-body">
                    <a href="{{ route('batches.medication-schedule', $batch) }}" class="btn btn-agri mb-3">
                        <i class="fas fa-calendar-check me-2"></i>View Full Schedule
                    </a>
                    <p class="text-muted">This batch has a medication schedule assigned. View all scheduled medications and tasks.</p>
                </div>
            </div>
        @else
            <div class="agri-card">
                <div class="agri-card-header" style="background: linear-gradient(135deg, rgba(255, 152, 0, 0.9), rgba(255, 193, 7, 0.9));">
                    <h3><i class="fas fa-calendar-plus me-2"></i>Medication Calendar</h3>
                </div>
                <div class="agri-card-body">
                    <p class="mb-3">Assign a medication calendar to automatically generate medication tasks for this batch.</p>
                    <a href="{{ route('batches.assign-medication', $batch) }}" class="btn btn-agri">
                        <i class="fas fa-plus me-2"></i>Assign Medication Calendar
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection