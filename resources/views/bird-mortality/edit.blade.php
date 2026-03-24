@extends('layouts.app')

@section('title', 'Edit Mortality Record')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Mortality Record</h1>
    <p class="page-subtitle">Update bird mortality record for {{ \Carbon\Carbon::parse($bird_mortality->record_date)->format('M d, Y') }}</p>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <form method="POST" action="{{ route('bird-mortality.update', $bird_mortality) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Bird Batch <span class="text-danger">*</span></label>
                    <select name="bird_batch_id" class="form-select @error('bird_batch_id') is-invalid @enderror" required>
                        <option value="">Select Batch</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}" {{ old('bird_batch_id', $bird_mortality->bird_batch_id) == $batch->id ? 'selected' : '' }}>
                                {{ $batch->batch_code }} 
                                @if($batch->house)
                                    - {{ $batch->house->name }} ({{ $batch->house->farm->name ?? '' }})
                                @endif
                                - {{ number_format($batch->remaining_birds) }} remaining
                            </option>
                        @endforeach
                    </select>
                    @error('bird_batch_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Record Date <span class="text-danger">*</span></label>
                    <input type="date" name="record_date" class="form-control @error('record_date') is-invalid @enderror" 
                           value="{{ old('record_date', $bird_mortality->record_date ? \Carbon\Carbon::parse($bird_mortality->record_date)->format('Y-m-d') : '') }}" required>
                    @error('record_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mortality Count <span class="text-danger">*</span></label>
                    <input type="number" name="mortality_count" class="form-control @error('mortality_count') is-invalid @enderror" 
                           value="{{ old('mortality_count', $bird_mortality->mortality_count) }}" min="0" required>
                    <small class="text-muted">Number of birds that died naturally</small>
                    @error('mortality_count')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Cull Count</label>
                    <input type="number" name="cull_count" class="form-control @error('cull_count') is-invalid @enderror" 
                           value="{{ old('cull_count', $bird_mortality->cull_count) }}" min="0">
                    <small class="text-muted">Number of birds culled (intentionally removed)</small>
                    @error('cull_count')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr class="my-4">
            <h5 class="mb-3"><i class="fas fa-utensils me-2 text-info"></i>Daily Consumption (Optional)</h5>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Feed Used (kg)</label>
                    <input type="number" step="0.1" name="feed_used_kg" class="form-control @error('feed_used_kg') is-invalid @enderror" 
                           value="{{ old('feed_used_kg', $bird_mortality->feed_used_kg) }}" min="0">
                    @error('feed_used_kg')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Water Used (Litres)</label>
                    <input type="number" step="0.1" name="water_used_litres" class="form-control @error('water_used_litres') is-invalid @enderror" 
                           value="{{ old('water_used_litres', $bird_mortality->water_used_litres) }}" min="0">
                    @error('water_used_litres')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Average Weight (kg)</label>
                    <input type="number" step="0.01" name="average_weight_kg" class="form-control @error('average_weight_kg') is-invalid @enderror" 
                           value="{{ old('average_weight_kg', $bird_mortality->average_weight_kg) }}" min="0">
                    @error('average_weight_kg')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $bird_mortality->notes) }}</textarea>
                <small class="text-muted">Any observations or additional information</small>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('bird-mortality.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Record
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
