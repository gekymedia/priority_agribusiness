@extends('layouts.app')

@section('title', 'Add Bird Batch')

@section('content')
<div class="page-header">
    <h1 class="page-title">Add Bird Batch</h1>
    <p class="page-subtitle">Create a new bird batch and optionally assign a medication calendar</p>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <form method="POST" action="{{ route('batches.store') }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="farm_id" class="form-label">
                        <i class="fas fa-tractor me-2"></i>Farm
                    </label>
                    <select name="farm_id" id="farm_id" class="form-select @error('farm_id') is-invalid @enderror" required>
                        <option value="">Select Farm...</option>
                        @foreach($farms as $farm)
                            <option value="{{ $farm->id }}" {{ old('farm_id') == $farm->id ? 'selected' : '' }}>{{ $farm->name }}</option>
                        @endforeach
                    </select>
                    @error('farm_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="house_id" class="form-label">
                        <i class="fas fa-home me-2"></i>House/Pen
                    </label>
                    <select name="house_id" id="house_id" class="form-select @error('house_id') is-invalid @enderror" required>
                        <option value="">Select House...</option>
                        @foreach($houses as $house)
                            <option value="{{ $house->id }}" {{ old('house_id') == $house->id ? 'selected' : '' }}>{{ $house->name }} ({{ $house->farm->name }})</option>
                        @endforeach
                    </select>
                    @error('house_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="batch_code" class="form-label">
                        <i class="fas fa-barcode me-2"></i>Batch Code
                    </label>
                    <input type="text" name="batch_code" id="batch_code" value="{{ old('batch_code') }}" class="form-control @error('batch_code') is-invalid @enderror" required>
                    @error('batch_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="breed" class="form-label">
                        <i class="fas fa-dove me-2"></i>Breed
                    </label>
                    <input type="text" name="breed" id="breed" value="{{ old('breed') }}" class="form-control @error('breed') is-invalid @enderror">
                    @error('breed')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="purpose" class="form-label">
                        <i class="fas fa-tag me-2"></i>Purpose
                    </label>
                    <select name="purpose" id="purpose" class="form-select @error('purpose') is-invalid @enderror" required>
                        <option value="broiler" {{ old('purpose') == 'broiler' ? 'selected' : '' }}>Broiler</option>
                        <option value="layer" {{ old('purpose') == 'layer' ? 'selected' : '' }}>Layer</option>
                        <option value="breeder" {{ old('purpose') == 'breeder' ? 'selected' : '' }}>Breeder</option>
                    </select>
                    @error('purpose')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="arrival_date" class="form-label">
                        <i class="fas fa-calendar me-2"></i>Arrival Date
                    </label>
                    <input type="date" name="arrival_date" id="arrival_date" value="{{ old('arrival_date') }}" class="form-control @error('arrival_date') is-invalid @enderror" required>
                    @error('arrival_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="quantity_arrived" class="form-label">
                        <i class="fas fa-hashtag me-2"></i>Quantity Arrived
                    </label>
                    <input type="number" name="quantity_arrived" id="quantity_arrived" value="{{ old('quantity_arrived') }}" class="form-control @error('quantity_arrived') is-invalid @enderror" required>
                    @error('quantity_arrived')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="cost_per_bird" class="form-label">
                        <i class="fas fa-money-bill me-2"></i>Cost per Bird
                    </label>
                    <input type="number" step="0.01" name="cost_per_bird" id="cost_per_bird" value="{{ old('cost_per_bird') }}" class="form-control @error('cost_per_bird') is-invalid @enderror">
                    @error('cost_per_bird')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="supplier_name" class="form-label">
                        <i class="fas fa-truck me-2"></i>Supplier Name
                    </label>
                    <input type="text" name="supplier_name" id="supplier_name" value="{{ old('supplier_name') }}" class="form-control @error('supplier_name') is-invalid @enderror">
                    @error('supplier_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="status" class="form-label">
                        <i class="fas fa-info-circle me-2"></i>Status
                    </label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="culled" {{ old('status') == 'culled' ? 'selected' : '' }}>Culled</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <hr class="my-4">
                    <h5 class="mb-3"><i class="fas fa-calendar-alt me-2"></i>Medication Calendar (Optional)</h5>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> Selecting a medication calendar will automatically create tasks for all scheduled medications based on the arrival date.
                    </div>
                    <label for="medication_calendar_id" class="form-label">
                        <i class="fas fa-pills me-2"></i>Select Medication Calendar
                    </label>
                    <select name="medication_calendar_id" id="medication_calendar_id" class="form-select @error('medication_calendar_id') is-invalid @enderror">
                        <option value="">No medication calendar (assign later)</option>
                        @foreach($medicationCalendars as $calendar)
                            <option value="{{ $calendar->id }}" {{ old('medication_calendar_id') == $calendar->id ? 'selected' : '' }}>
                                {{ $calendar->name }} 
                                @if($calendar->type)
                                    ({{ ucfirst($calendar->type) }})
                                @endif
                                - {{ count($calendar->schedule ?? []) }} medications
                            </option>
                        @endforeach
                    </select>
                    @error('medication_calendar_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">You can view all medication calendar templates from the Medication Calendars menu.</small>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-agri">
                    <i class="fas fa-save me-2"></i>Create Batch
                </button>
                <a href="{{ route('batches.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection