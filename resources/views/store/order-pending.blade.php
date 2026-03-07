@extends('layouts.guest')

@section('title', 'Order placed')

@section('subtitle', 'Order ' . $order->order_number)

@section('auth-content')
<div class="container py-4 text-center">
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif
    <div class="card border-0 shadow-sm mx-auto" style="max-width: 480px;">
        <div class="card-body p-4">
            <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-inline-flex p-4 mb-3"><i class="fas fa-clock fa-3x"></i></div>
            <h1 class="h4 mb-2">Order placed</h1>
            <p class="text-muted mb-3">Order number: <strong>{{ $order->order_number }}</strong></p>
            <p class="mb-0">We have received your order. If you were not redirected to complete payment, please check your email or contact us with this order number.</p>
        </div>
    </div>
    <a href="{{ route('store.index') }}" class="btn btn-primary mt-4">Back to store</a>
</div>
@endsection
