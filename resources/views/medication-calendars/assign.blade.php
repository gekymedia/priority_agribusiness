@extends('layouts.app')

@section('title', 'Assign Medication Calendar')

@section('content')
<div class="page-header">
    <h1 class="page-title">Assign Medication Calendar</h1>
    <p class="page-subtitle">Select a medication schedule for batch: {{ $batch->batch_code }}</p>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <div class="alert alert-modern alert-info mb-4">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Note:</strong> Assigning a medication calendar will automatically create tasks for each scheduled medication. 
            Tasks will be linked to your task list and you'll receive notifications for upcoming medications.
        </div>

        <form method="POST" action="{{ route('batches.assign-medication.store', $batch) }}">
            @csrf

            <div class="mb-4">
                <label for="medication_calendar_id" class="form-label">
                    <i class="fas fa-calendar me-2"></i>Select Medication Calendar
                </label>
                <select class="form-select @error('medication_calendar_id') is-invalid @enderror" 
                        id="medication_calendar_id" 
                        name="medication_calendar_id" 
                        required>
                    <option value="">Choose a calendar...</option>
                    @foreach($calendars as $calendar)
                        <option value="{{ $calendar->id }}" {{ old('medication_calendar_id') == $calendar->id ? 'selected' : '' }}>
                            {{ $calendar->name }} 
                            @if($calendar->type)
                                ({{ ucfirst($calendar->type) }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('medication_calendar_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            @if($calendars->count() > 0)
                <div class="mb-4">
                    <h5 class="mb-3">Available Calendars:</h5>
                    <div class="row g-3">
                        @foreach($calendars as $calendar)
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $calendar->name }}</h6>
                                        <p class="card-text text-muted small">{{ $calendar->description ?? 'No description' }}</p>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="fas fa-list me-1"></i>
                                                {{ count($calendar->schedule ?? []) }} scheduled medications
                                            </small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-agri">
                    <i class="fas fa-check me-2"></i>Assign Calendar & Create Tasks
                </button>
                <a href="{{ route('batches.show', $batch) }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

