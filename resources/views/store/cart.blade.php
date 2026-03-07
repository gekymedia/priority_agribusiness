@extends('layouts.guest')

@section('title', 'Cart')

@section('subtitle', 'Review your order')

@section('auth-content')
<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Your Cart</h1>
        <a href="{{ route('store.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Continue shopping</a>
    </div>

    @if(empty($items))
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <p class="text-muted">Your cart is empty.</p>
                <a href="{{ route('store.index') }}" class="btn btn-primary">Browse eggs</a>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($items as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $item['unit_type'] === 'crate' ? 'Crate' : 'Piece' }}</strong>
                                <span class="text-muted">× {{ $item['quantity'] }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="text-primary fw-bold">₵{{ number_format($item['total'], 2) }}</span>
                                <a href="{{ route('store.cart.remove', $item['unit_type']) }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this item?');"><i class="fas fa-trash"></i></a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="card-footer bg-white border-0 pt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="h5 mb-0">Total</span>
                    <span class="h4 text-primary mb-0">₵{{ number_format($subtotal, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2 justify-content-end">
            <a href="{{ route('store.index') }}" class="btn btn-outline-secondary">Continue shopping</a>
            <a href="{{ route('store.checkout') }}" class="btn btn-primary"><i class="fas fa-lock me-1"></i> Proceed to checkout</a>
        </div>
    @endif
</div>
@endsection
