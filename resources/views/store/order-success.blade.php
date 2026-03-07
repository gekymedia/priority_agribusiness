@extends('layouts.guest')

@section('title', 'Thank you')

@section('subtitle', 'Order ' . $order->order_number)

@section('auth-content')
<div class="container py-4 text-center">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card border-0 shadow-sm mx-auto" style="max-width: 480px;">
        <div class="card-body p-4">
            <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex p-4 mb-3"><i class="fas fa-check fa-3x"></i></div>
            <h1 class="h4 mb-2">Thank you!</h1>
            <p class="text-muted mb-3">Order number: <strong>{{ $order->order_number }}</strong></p>
            <p class="mb-0">Your payment was successful. We will process your order shortly. @if($order->wants_delivery) Delivery fee will be collected when your order is delivered. @endif</p>
        </div>
    </div>
    <a href="{{ route('store.index') }}" class="btn btn-primary mt-4">Buy more eggs</a>
</div>
@endsection
