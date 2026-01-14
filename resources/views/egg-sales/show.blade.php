@extends('layouts.app')

@section('title', 'View Egg Sale')

@section('content')
<div class="page-header">
    <h1 class="page-title">Egg Sale Details</h1>
    <p class="page-subtitle">Sale transaction information</p>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="agri-card">
            <div class="agri-card-header">
                <h3><i class="fas fa-shopping-cart me-2"></i>Sale Information</h3>
            </div>
            <div class="agri-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted">Sale Date</label>
                        <p class="h5">{{ $eggSale->date->format('F d, Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted">Bird Batch</label>
                        <p class="h5">{{ $eggSale->birdBatch->batch_code ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted">Farm</label>
                        <p class="h5">{{ $eggSale->birdBatch->farm->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted">Quantity Sold</label>
                        <p class="h5 text-primary">{{ number_format($eggSale->quantity_sold) }} {{ ucfirst($eggSale->unit_type) }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted">Price per Unit</label>
                        <p class="h5">₵{{ number_format($eggSale->price_per_unit, 2) }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted">Total Amount</label>
                        <p class="h4 text-success">
                            <strong>₵{{ number_format($eggSale->quantity_sold * $eggSale->price_per_unit, 2) }}</strong>
                        </p>
                    </div>
                    @if($eggSale->buyer_name)
                    <div class="col-md-6">
                        <label class="text-muted">Buyer Name</label>
                        <p class="h6">{{ $eggSale->buyer_name }}</p>
                    </div>
                    @endif
                    @if($eggSale->buyer_contact)
                    <div class="col-md-6">
                        <label class="text-muted">Buyer Contact</label>
                        <p class="h6">{{ $eggSale->buyer_contact }}</p>
                    </div>
                    @endif
                    @if($eggSale->notes)
                    <div class="col-12">
                        <label class="text-muted">Notes</label>
                        <p>{{ $eggSale->notes }}</p>
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
                    <a href="{{ route('egg-sales.edit', $eggSale) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit Sale
                    </a>
                    <form action="{{ route('egg-sales.destroy', $eggSale) }}" method="POST" onsubmit="return confirm('Delete this sale?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash me-2"></i>Delete
                        </button>
                    </form>
                    <a href="{{ route('egg-sales.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
