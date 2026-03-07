@extends('layouts.guest')

@section('title', 'Buy Eggs')

@section('subtitle', 'Fresh eggs by crate or piece')

@section('auth-content')
<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Egg Store</h1>
        <a href="{{ route('store.cart') }}" class="btn btn-outline-primary">
            <i class="fas fa-shopping-cart me-1"></i> Cart @if($cartCount > 0)<span class="badge bg-primary">{{ $cartCount }}</span>@endif
        </a>
    </div>

    @if(!$gateway)
        <div class="alert alert-warning">Online payment is not configured. Please contact us to place an order.</div>
    @elseif($priceCrate <= 0 && $pricePiece <= 0)
        <div class="alert alert-info">Prices are not set yet. Check back soon.</div>
    @else
        <div class="row g-4">
            @if($priceCrate > 0)
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm border-0" style="border-radius: 16px;">
                        <div class="card-body text-center p-4">
                            <div class="rounded-3 bg-success bg-opacity-10 text-success d-inline-flex p-3 mb-3"><i class="fas fa-box-open fa-2x"></i></div>
                            <h5 class="card-title">Per Crate</h5>
                            <p class="text-muted small mb-2">{{ $eggsPerCrate }} eggs per crate</p>
                            <p class="h4 text-primary mb-3">₵{{ number_format($priceCrate, 2) }} <small class="text-muted">/ crate</small></p>
                            <form action="{{ route('store.add-to-cart') }}" method="POST" class="d-flex align-items-center justify-content-center gap-2">
                                @csrf
                                <input type="hidden" name="unit_type" value="crate">
                                <input type="number" name="quantity" value="1" min="1" max="50" class="form-control form-control-sm" style="width: 80px;">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-cart-plus me-1"></i> Add to cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
            @if($pricePiece > 0)
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm border-0" style="border-radius: 16px;">
                        <div class="card-body text-center p-4">
                            <div class="rounded-3 bg-warning bg-opacity-10 text-warning d-inline-flex p-3 mb-3"><i class="fas fa-egg fa-2x"></i></div>
                            <h5 class="card-title">Per Piece</h5>
                            <p class="text-muted small mb-2">Buy individual eggs</p>
                            <p class="h4 text-primary mb-3">₵{{ number_format($pricePiece, 2) }} <small class="text-muted">/ piece</small></p>
                            <form action="{{ route('store.add-to-cart') }}" method="POST" class="d-flex align-items-center justify-content-center gap-2">
                                @csrf
                                <input type="hidden" name="unit_type" value="piece">
                                <input type="number" name="quantity" value="1" min="1" max="200" class="form-control form-control-sm" style="width: 80px;">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-cart-plus me-1"></i> Add to cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <p class="text-muted small mt-4 text-center">Pay securely with {{ $gateway === 'hubtel' ? 'Hubtel' : 'Paystack' }} (cards & mobile money). Delivery option available at checkout.</p>
    @endif
</div>
@endsection
