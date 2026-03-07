@extends('layouts.app')

@section('title', 'Bulk Import Egg Production')

@section('content')
<div class="page-header">
    <h1 class="page-title">Bulk Import Egg Production</h1>
    <p class="page-subtitle">Paste daily egg counts from your caretaker (one line per day, e.g. "19th January 0 eggs")</p>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="agri-card">
    <div class="agri-card-body">
        <form method="POST" action="{{ route('egg-productions.bulk-import.process') }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="bird_batch_id" class="form-label">
                        <i class="fas fa-dove me-2"></i>Bird Batch (layer)
                    </label>
                    <select name="bird_batch_id" id="bird_batch_id" class="form-select @error('bird_batch_id') is-invalid @enderror" required>
                        <option value="">Select batch for this production data</option>
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
                    <label for="year" class="form-label">
                        <i class="fas fa-calendar me-2"></i>Year for dates without year
                    </label>
                    <input type="number" name="year" id="year" class="form-control @error('year') is-invalid @enderror" value="{{ old('year', date('Y')) }}" min="2020" max="2030">
                    @error('year')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Used when lines don't include a year (e.g. "19th January 0 eggs").</small>
                </div>

                <div class="col-12">
                    <label for="pasted_data" class="form-label">
                        <i class="fas fa-paste me-2"></i>Paste daily production (one line per day)
                    </label>
                    <textarea name="pasted_data" id="pasted_data" class="form-control font-monospace @error('pasted_data') is-invalid @enderror" rows="14" placeholder="19th January 0 eggs&#10;20th January 0 eggs&#10;22nd January 3 eggs&#10;23rd January 1 damage egg&#10;...">{{ old('pasted_data') }}</textarea>
                    @error('pasted_data')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Supported: "X eggs", "X egg", "1 damage egg", "crack 1 egg", "7 eggs 1 broken". Dates already in the system for this batch are skipped.</small>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-file-import me-2"></i>Import Records
                </button>
                <a href="{{ route('egg-productions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
                <a href="{{ route('egg-productions.create') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-plus me-2"></i>Add single record
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
