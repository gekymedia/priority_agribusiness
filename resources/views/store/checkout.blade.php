@extends('layouts.guest')

@section('title', 'Checkout')

@section('subtitle', 'Delivery & payment')

@section('auth-content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h2 class="h5 mb-0">Order summary</h2>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach($items as $item)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $item['unit_type'] === 'crate' ? 'Crate' : 'Piece' }} × {{ $item['quantity'] }}</span>
                            <span>₵{{ number_format($item['total'], 2) }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="card-footer bg-white d-flex justify-content-between">
                    <strong>Total</strong>
                    <strong class="text-primary">₵{{ number_format($subtotal, 2) }}</strong>
                </div>
            </div>

            <form action="{{ route('store.checkout.process') }}" method="POST" class="card border-0 shadow-sm">
                @csrf
                <div class="card-body">
                    <h3 class="h6 mb-3">Your details</h3>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="customer_name" class="form-label">Full name <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control" value="{{ old('customer_name') }}" required>
                            @error('customer_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="customer_email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="customer_email" id="customer_email" class="form-control" value="{{ old('customer_email') }}" required>
                            @error('customer_email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="customer_phone" class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" name="customer_phone" id="customer_phone" class="form-control" value="{{ old('customer_phone') }}" required>
                            @error('customer_phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="wants_delivery" id="wants_delivery" value="1" {{ old('wants_delivery') ? 'checked' : '' }}>
                        <label class="form-check-label" for="wants_delivery">I want delivery</label>
                    </div>
                    <div id="deliveryFields" style="{{ old('wants_delivery') ? '' : 'display:none;' }}">
                        <div class="mb-3">
                            <label for="delivery_address" class="form-label">Delivery address</label>
                            <textarea name="delivery_address" id="delivery_address" class="form-control" rows="2">{{ old('delivery_address') }}</textarea>
                            @error('delivery_address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="delivery_notes" class="form-label">Delivery notes (e.g. landmark)</label>
                            <input type="text" name="delivery_notes" id="delivery_notes" class="form-control" value="{{ old('delivery_notes') }}">
                        </div>
                        <p class="small text-muted mb-0">
                            <i class="fas fa-info-circle me-1"></i> <strong>Note:</strong> Delivery fee will be collected by the delivery person when your order is delivered.
                        </p>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-lock me-2"></i> Pay with {{ $gateway === 'hubtel' ? 'Hubtel' : 'Paystack' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.getElementById('wants_delivery').addEventListener('change', function() {
    document.getElementById('deliveryFields').style.display = this.checked ? 'block' : 'none';
});
</script>
@endsection
