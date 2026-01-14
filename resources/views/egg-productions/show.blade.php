@extends('layouts.app')

@section('title', 'View Egg Production')

@section('content')
<div class="page-header">
    <h1 class="page-title">Egg Production Details</h1>
    <p class="page-subtitle">Production record information</p>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="agri-card">
            <div class="agri-card-header">
                <h3><i class="fas fa-egg me-2"></i>Production Information</h3>
            </div>
            <div class="agri-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted">Date</label>
                        <p class="h5">{{ $eggProduction->date->format('F d, Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted">Bird Batch</label>
                        <p class="h5">{{ $eggProduction->birdBatch->batch_code ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted">Farm</label>
                        <p class="h5">{{ $eggProduction->birdBatch->farm->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted">Total Eggs Collected</label>
                        <p class="h5 text-primary">{{ number_format($eggProduction->eggs_collected) }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted">Cracked/Damaged</label>
                        <p class="h6 text-danger">{{ number_format($eggProduction->cracked_or_damaged) }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted">Used Internal</label>
                        <p class="h6 text-warning">{{ number_format($eggProduction->eggs_used_internal) }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted">Available for Sale</label>
                        <p class="h6 text-success">
                            <strong>{{ number_format($eggProduction->eggs_collected - $eggProduction->cracked_or_damaged - $eggProduction->eggs_used_internal) }}</strong>
                        </p>
                    </div>
                    @if($eggProduction->notes)
                    <div class="col-12">
                        <label class="text-muted">Notes</label>
                        <p>{{ $eggProduction->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="agri-card">
            <div class="agri-card-body">
                <h5 class="mb-3">Actions</h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('egg-productions.edit', $eggProduction) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit Record
                    </a>
                    <form action="{{ route('egg-productions.destroy', $eggProduction) }}" method="POST" onsubmit="return confirm('Delete this record?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash me-2"></i>Delete
                        </button>
                    </form>
                    <a href="{{ route('egg-productions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
