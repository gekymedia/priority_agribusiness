@extends('layouts.app')

@section('title', 'Add Egg Production')

@section('content')
<div class="page-header">
    <h1 class="page-title">Add Egg Production Record</h1>
    <p class="page-subtitle">Record daily egg collection from your layer batches</p>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <form method="POST" action="{{ route('egg-productions.store') }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="bird_batch_id" class="form-label">
                        <i class="fas fa-dove me-2"></i>Bird Batch
                    </label>
                    <select name="bird_batch_id" id="bird_batch_id" class="form-select @error('bird_batch_id') is-invalid @enderror" required>
                        <option value="">Select Batch</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}" {{ old('bird_batch_id') == $batch->id ? 'selected' : '' }}>
                                {{ $batch->batch_code }} - {{ $batch->farm->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('bird_batch_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="date" class="form-label">
                        <i class="fas fa-calendar me-2"></i>Date
                    </label>
                    <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', date('Y-m-d')) }}" required>
                    @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="eggs_collected" class="form-label">
                        <i class="fas fa-egg me-2"></i>Total Eggs Collected
                    </label>
                    <input type="number" name="eggs_collected" id="eggs_collected" class="form-control @error('eggs_collected') is-invalid @enderror" value="{{ old('eggs_collected') }}" min="0" required>
                    @error('eggs_collected')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="cracked_or_damaged" class="form-label">
                        <i class="fas fa-exclamation-triangle me-2"></i>Cracked/Damaged
                    </label>
                    <input type="number" name="cracked_or_damaged" id="cracked_or_damaged" class="form-control @error('cracked_or_damaged') is-invalid @enderror" value="{{ old('cracked_or_damaged', 0) }}" min="0" required>
                    @error('cracked_or_damaged')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="eggs_used_internal" class="form-label">
                        <i class="fas fa-home me-2"></i>Used Internal
                    </label>
                    <input type="number" name="eggs_used_internal" id="eggs_used_internal" class="form-control @error('eggs_used_internal') is-invalid @enderror" value="{{ old('eggs_used_internal', 0) }}" min="0" required>
                    @error('eggs_used_internal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="notes" class="form-label">
                        <i class="fas fa-sticky-note me-2"></i>Notes
                    </label>
                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Save Record
                </button>
                <a href="{{ route('egg-productions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
