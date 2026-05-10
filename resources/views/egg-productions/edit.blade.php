@extends('layouts.app')

@section('title', 'Edit Egg Production')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Egg Production Record</h1>
    <p class="page-subtitle">Update egg production information</p>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <form method="POST" action="{{ route('egg-productions.update', $eggProduction) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="bird_batch_id" class="form-label">
                        <i class="fas fa-dove me-2"></i>Bird Batch
                    </label>
                    <select name="bird_batch_id" id="bird_batch_id" class="form-select @error('bird_batch_id') is-invalid @enderror" required>
                        <option value="">Select Batch</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}" {{ old('bird_batch_id', $eggProduction->bird_batch_id) == $batch->id ? 'selected' : '' }}>
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
                    <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', $eggProduction->date->format('Y-m-d')) }}" required>
                    @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <input type="hidden" name="egg_size_breakdown" value="0">
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" name="egg_size_breakdown" id="egg_size_breakdown" class="form-check-input" value="1" {{ old('egg_size_breakdown', $eggProduction->egg_size_breakdown ? '1' : '0') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="egg_size_breakdown">Record saleable eggs by size (large / medium / small)</label>
                    </div>
                    <p class="text-muted small mb-0" id="egg_breakdown_hint" style="display:none;">Large, medium, and small count only the eggs left after cracked and internal use are accounted for in the total. Total collected is calculated automatically.</p>
                </div>

                <div class="col-md-4" id="egg_total_manual_wrap">
                    <label for="eggs_collected" class="form-label">
                        <i class="fas fa-egg me-2"></i>Total Eggs Collected
                    </label>
                    <input type="number" name="eggs_collected" id="eggs_collected" class="form-control @error('eggs_collected') is-invalid @enderror" value="{{ old('eggs_collected', $eggProduction->eggs_collected) }}" min="0" required>
                    @error('eggs_collected')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="cracked_or_damaged" class="form-label">
                        <i class="fas fa-exclamation-triangle me-2"></i>Cracked/Damaged
                    </label>
                    <input type="number" name="cracked_or_damaged" id="cracked_or_damaged" class="form-control @error('cracked_or_damaged') is-invalid @enderror" value="{{ old('cracked_or_damaged', $eggProduction->cracked_or_damaged) }}" min="0" required>
                    @error('cracked_or_damaged')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="eggs_used_internal" class="form-label">
                        <i class="fas fa-home me-2"></i>Used Internal
                    </label>
                    <input type="number" name="eggs_used_internal" id="eggs_used_internal" class="form-control @error('eggs_used_internal') is-invalid @enderror" value="{{ old('eggs_used_internal', $eggProduction->eggs_used_internal) }}" min="0" required>
                    @error('eggs_used_internal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12" id="egg_size_breakdown_wrap" style="display:none;">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="eggs_large" class="form-label">Large</label>
                            <input type="number" name="eggs_large" id="eggs_large" class="form-control @error('eggs_large') is-invalid @enderror" value="{{ old('eggs_large', $eggProduction->eggs_large) }}" min="0">
                            @error('eggs_large')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="eggs_medium" class="form-label">Medium</label>
                            <input type="number" name="eggs_medium" id="eggs_medium" class="form-control @error('eggs_medium') is-invalid @enderror" value="{{ old('eggs_medium', $eggProduction->eggs_medium) }}" min="0">
                            @error('eggs_medium')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="eggs_small" class="form-label">Small</label>
                            <input type="number" name="eggs_small" id="eggs_small" class="form-control @error('eggs_small') is-invalid @enderror" value="{{ old('eggs_small', $eggProduction->eggs_small) }}" min="0">
                            @error('eggs_small')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <p class="mt-2 mb-0"><strong>Total eggs collected:</strong> <span id="egg_total_computed_display">0</span></p>
                </div>

                <div class="col-12">
                    <label for="notes" class="form-label">
                        <i class="fas fa-sticky-note me-2"></i>Notes
                    </label>
                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $eggProduction->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Record
                </button>
                <a href="{{ route('egg-productions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    var cb = document.getElementById('egg_size_breakdown');
    var manualWrap = document.getElementById('egg_total_manual_wrap');
    var breakdownWrap = document.getElementById('egg_size_breakdown_wrap');
    var hint = document.getElementById('egg_breakdown_hint');
    var eggsCollected = document.getElementById('eggs_collected');
    var cracked = document.getElementById('cracked_or_damaged');
    var internal = document.getElementById('eggs_used_internal');
    var large = document.getElementById('eggs_large');
    var medium = document.getElementById('eggs_medium');
    var small = document.getElementById('eggs_small');
    var totalDisplay = document.getElementById('egg_total_computed_display');
    if (!cb || !manualWrap || !breakdownWrap) return;

    function num(el) { return parseInt(el.value, 10) || 0; }

    function syncBreakdownUi() {
        var on = cb.checked;
        manualWrap.style.display = on ? 'none' : '';
        breakdownWrap.style.display = on ? '' : 'none';
        if (hint) hint.style.display = on ? '' : 'none';
        if (eggsCollected) {
            eggsCollected.required = !on;
            eggsCollected.disabled = on;
            if (!on) eggsCollected.removeAttribute('disabled');
        }
        if (on) updateComputedTotal();
    }

    function updateComputedTotal() {
        if (!totalDisplay) return;
        var t = num(large) + num(medium) + num(small) + num(cracked) + num(internal);
        totalDisplay.textContent = t.toLocaleString();
    }

    cb.addEventListener('change', syncBreakdownUi);
    [cracked, internal, large, medium, small].forEach(function(el) {
        if (el) el.addEventListener('input', updateComputedTotal);
    });
    syncBreakdownUi();
})();
</script>
@endpush
