@extends('layouts.app')

@section('title', 'Bulk Import Egg Sales')

@section('content')
<div class="page-header">
    <h1 class="page-title">Bulk Import Egg Sales</h1>
    <p class="page-subtitle">Paste a client sales report — sections, line items, and payment totals are parsed automatically.</p>
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

<div class="agri-card mb-4">
    <div class="agri-card-body">
        <form method="POST" action="{{ route('egg-sales.bulk-import.process') }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="bird_batch_id" class="form-label">
                        <i class="fas fa-dove me-2"></i>Bird Batch
                    </label>
                    <select name="bird_batch_id" id="bird_batch_id" class="form-select @error('bird_batch_id') is-invalid @enderror" required>
                        <option value="">Select batch for these sales</option>
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
                        <i class="fas fa-calendar me-2"></i>Sale Date
                    </label>
                    <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror"
                           value="{{ old('date', date('Y-m-d')) }}" required>
                    @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Applied to all imported client sales unless noted on a line.</small>
                </div>

                <div class="col-12">
                    <label for="pasted_data" class="form-label">
                        <i class="fas fa-paste me-2"></i>Paste sales report
                    </label>
                    <textarea name="pasted_data" id="pasted_data" class="form-control font-monospace @error('pasted_data') is-invalid @enderror" rows="18" placeholder="#### A. PRISCILLA'S ORDER&#10;1. Small Eggs — 10 x 35 = 350 GHS (Paid)&#10;2. Small Eggs — 2 x 35 = 70 GHS (Paid)&#10;3. Small Eggs — 3 x 35 = 105 GHS (Unpaid)&#10;* Subtotal: Paid: 420 | Unpaid: 105 | Total: 525&#10;&#10;#### D. HOPE&#10;1. Small Eggs — 9 x 35 = 315 GHS&#10;* Payments Received: 703">{{ old('pasted_data') }}</textarea>
                    @error('pasted_data')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input type="hidden" name="skip_duplicates" value="0">
                        <input type="checkbox" name="skip_duplicates" id="skip_duplicates" class="form-check-input" value="1"
                               {{ old('skip_duplicates', '1') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="skip_duplicates">
                            Skip clients that already exist for this batch and date
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-file-import me-2"></i>Import Client Sales
                </button>
                <a href="{{ route('egg-sales.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
                <a href="{{ route('egg-sales.create') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-plus me-2"></i>Record single sale
                </a>
            </div>
        </form>
    </div>
</div>

<div class="agri-card">
    <div class="agri-card-header">
        <h3 class="h6 mb-0"><i class="fas fa-info-circle me-2"></i>Supported formats</h3>
    </div>
    <div class="agri-card-body">
        <p class="mb-2"><strong>1. Sales report (from photos / assistant transcript)</strong></p>
        <pre class="bg-light p-3 rounded small mb-3">#### A. PRISCILLA'S ORDER
1. Small Eggs — 10 x 35 = 350 GHS (Paid)
3. Small Eggs — 3 x 35 = 105 GHS (Unpaid)
* Subtotal: Paid: 420 | Unpaid: 105 | Total: 525

#### D. HOPE
1. Small Eggs — 9 x 35 = 315 GHS
* Payments Received: 703</pre>

        <p class="mb-2"><strong>2. Bracket format (manual entry)</strong></p>
        <pre class="bg-light p-3 rounded small mb-0">[Hope | received: 703]
small, 9, 35, paid
medium, 1, 38, paid
small, 6, 35, paid</pre>
        <p class="text-muted small mt-2 mb-0">Sizes: small, medium, large. Payment per line: paid or unpaid. Use <code>received:</code> for partial client payments.</p>
    </div>
</div>
@endsection
