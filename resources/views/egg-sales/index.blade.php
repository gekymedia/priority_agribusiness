@extends('layouts.app')

@section('title', 'Egg Sales')

@section('content')
<div class="page-header">
    <h1 class="page-title">Egg Sales</h1>
    <p class="page-subtitle">Track all egg sales transactions</p>
</div>

@php
    $activeTab = request('tab', 'recorded');
@endphp

<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'recorded' ? 'active' : '' }}" href="{{ route('egg-sales.index', ['tab' => 'recorded']) }}">
            <i class="fas fa-list me-1"></i> Recorded Sales
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'online' ? 'active' : '' }}" href="{{ route('egg-sales.index', ['tab' => 'online']) }}">
            <i class="fas fa-shopping-cart me-1"></i> Online Store Sales
        </a>
    </li>
</ul>

@if($activeTab === 'recorded')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('egg-sales.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Record Sale
        </a>
    </div>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Batch</th>
                        <th>Farm</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Price/Unit</th>
                        <th>Total Amount</th>
                        <th>Buyer</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr>
                        <td>{{ $sale->date->format('M d, Y') }}</td>
                        <td>{{ $sale->birdBatch->batch_code ?? 'N/A' }}</td>
                        <td>{{ $sale->birdBatch->farm->name ?? 'N/A' }}</td>
                        <td><strong>{{ number_format($sale->quantity_sold) }}</strong></td>
                        <td><span class="badge bg-info">{{ ucfirst($sale->unit_type) }}</span></td>
                        <td>₵{{ number_format($sale->price_per_unit, 2) }}</td>
                        <td><strong class="text-success">₵{{ number_format($sale->quantity_sold * $sale->price_per_unit, 2) }}</strong></td>
                        <td>{{ $sale->buyer_name ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('egg-sales.show', $sale) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('egg-sales.edit', $sale) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('egg-sales.destroy', $sale) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this sale?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No egg sales recorded</p>
                            <a href="{{ route('egg-sales.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Record First Sale
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sales->hasPages())
        <div class="mt-4">
            {{ $sales->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endif

@if($activeTab === 'online')
<div class="agri-card">
    <div class="agri-card-body">
        <p class="text-muted mb-3">
            <i class="fas fa-info-circle me-1"></i>
            Orders placed from the online store. Mark as <strong>Complete</strong> when you have given the eggs to the customer so the sale is recorded above.
        </p>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Contact</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($onlineOrders as $order)
                    <tr>
                        <td><strong>{{ $order->order_number }}</strong></td>
                        <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->customer_phone }}</td>
                        <td>
                            @foreach($order->items as $item)
                                <span class="badge bg-secondary">{{ $item->quantity }} {{ ucfirst($item->unit_type) }}(s)</span>
                            @endforeach
                        </td>
                        <td><strong class="text-success">₵{{ number_format($order->total_amount, 2) }}</strong></td>
                        <td>
                            @if($order->status === \App\Models\MarketOrder::STATUS_PENDING)
                                <span class="badge bg-warning text-dark">{{ $order->status_label }}</span>
                            @elseif($order->status === \App\Models\MarketOrder::STATUS_PAID)
                                <span class="badge bg-info">{{ $order->status_label }}</span>
                            @elseif($order->status === \App\Models\MarketOrder::STATUS_DELIVERED)
                                <span class="badge bg-success">{{ $order->status_label }}</span>
                            @else
                                <span class="badge bg-secondary">{{ $order->status_label }}</span>
                            @endif
                        </td>
                        <td>
                            @if($order->status === \App\Models\MarketOrder::STATUS_PAID)
                                <form action="{{ route('egg-sales.online-orders.complete', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Mark this order as complete? Eggs will be recorded in Recorded Sales.');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-check me-1"></i>Mark complete
                                    </button>
                                </form>
                            @elseif($order->status === \App\Models\MarketOrder::STATUS_DELIVERED)
                                <span class="text-muted small">Recorded in sales</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No online orders yet</p>
                            <p class="small text-muted">Orders from the store will appear here with status: Pending (Not paid), Paid, or Complete.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($onlineOrders->hasPages())
        <div class="mt-4">
            {{ $onlineOrders->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endif
@endsection
