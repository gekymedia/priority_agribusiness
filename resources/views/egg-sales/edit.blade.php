@extends('layouts.app')

@section('title', 'Edit Egg Sale')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Egg Sale</h1>
    <p class="page-subtitle">Update sale information</p>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <form method="POST" action="{{ route('egg-sales.update', $eggSale) }}">
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
                            <option value="{{ $batch->id }}" {{ old('bird_batch_id', $eggSale->bird_batch_id) == $batch->id ? 'selected' : '' }}>
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
                    <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', $eggSale->date->format('Y-m-d')) }}" required>
                    @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="quantity_sold" class="form-label">
                        <i class="fas fa-shopping-cart me-2"></i>Quantity Sold
                    </label>
                    <input type="number" name="quantity_sold" id="quantity_sold" class="form-control @error('quantity_sold') is-invalid @enderror" value="{{ old('quantity_sold', $eggSale->quantity_sold) }}" min="1" required>
                    @error('quantity_sold')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="unit_type" class="form-label">
                        <i class="fas fa-box me-2"></i>Unit Type
                    </label>
                    <select name="unit_type" id="unit_type" class="form-select @error('unit_type') is-invalid @enderror" required>
                        <option value="tray" {{ old('unit_type', $eggSale->unit_type) == 'tray' ? 'selected' : '' }}>Tray</option>
                        <option value="piece" {{ old('unit_type', $eggSale->unit_type) == 'piece' ? 'selected' : '' }}>Piece</option>
                        <option value="crate" {{ old('unit_type', $eggSale->unit_type) == 'crate' ? 'selected' : '' }}>Crate</option>
                    </select>
                    @error('unit_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="price_per_unit" class="form-label">
                        <i class="fas fa-money-bill me-2"></i>Price per Unit (₵)
                    </label>
                    <input type="number" name="price_per_unit" id="price_per_unit" step="0.01" class="form-control @error('price_per_unit') is-invalid @enderror" value="{{ old('price_per_unit', $eggSale->price_per_unit) }}" min="0" required>
                    @error('price_per_unit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="buyer_name" class="form-label">
                        <i class="fas fa-user me-2"></i>Buyer Name
                    </label>
                    <input type="text" name="buyer_name" id="buyer_name" class="form-control @error('buyer_name') is-invalid @enderror" value="{{ old('buyer_name', $eggSale->buyer_name) }}">
                    @error('buyer_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="buyer_contact" class="form-label">
                        <i class="fas fa-phone me-2"></i>Buyer Contact
                    </label>
                    <input type="text" name="buyer_contact" id="buyer_contact" class="form-control @error('buyer_contact') is-invalid @enderror" value="{{ old('buyer_contact', $eggSale->buyer_contact) }}">
                    @error('buyer_contact')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="notes" class="form-label">
                        <i class="fas fa-sticky-note me-2"></i>Notes
                    </label>
                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $eggSale->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Sale
                </button>
                <a href="{{ route('egg-sales.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
