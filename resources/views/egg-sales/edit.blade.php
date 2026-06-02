@extends('layouts.app')

@section('title', 'Edit Egg Sale')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Egg Sale</h1>
    <p class="page-subtitle">Update client sale for {{ $clientSale->buyer_name ?: 'buyer' }}</p>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <form method="POST" action="{{ route('egg-sales.update', $clientSale) }}">
            @csrf
            @method('PUT')
            @php
                $items = old('items', $clientSale->items->map(fn ($item) => [
                    'egg_size' => $item->egg_size,
                    'quantity' => $item->quantity_sold,
                    'price_per_unit' => $item->price_per_unit,
                    'payment_status' => $item->payment_status,
                    'line_notes' => $item->notes,
                ])->values()->all());
            @endphp
            @include('egg-sales._form', ['batches' => $batches, 'clientSale' => $clientSale, 'items' => $items])

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Sale
                </button>
                <a href="{{ route('egg-sales.show', $clientSale) }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
