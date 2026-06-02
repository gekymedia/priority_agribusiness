@extends('layouts.app')

@section('title', 'Egg Sales')

@section('content')
<div class="page-header">
    <h1 class="page-title">Egg Sales @include('egg-sales._stock_balance')</h1>
    <p class="page-subtitle">Track client egg sales with sizes and payment status</p>
</div>

@php
    $activeTab = request('tab', 'recorded');
    $sort = $sort ?? 'date';
    $direction = $direction ?? 'desc';
    $onlineSort = $onlineSort ?? 'created_at';
    $onlineDir = $onlineDir ?? 'desc';
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
            <i class="fas fa-plus me-2"></i>Record Client Sale
        </a>
        <a href="{{ route('egg-sales.bulk-import') }}" class="btn btn-outline-primary ms-2">
            <i class="fas fa-file-import me-2"></i>Bulk Import
        </a>
    </div>
</div>

<div class="agri-card mb-4">
    <div class="agri-card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        @php
                            $sortUrl = fn ($col) => request()->fullUrlWithQuery(['sort' => $col, 'direction' => ($sort === $col && $direction === 'asc') ? 'desc' : 'asc', 'page' => null]);
                            $sortIcon = fn ($col) => $sort === $col ? ($direction === 'asc' ? ' fa-sort-up' : ' fa-sort-down') : ' fa-sort text-muted';
                        @endphp
                        <th><a href="{{ $sortUrl('date') }}" class="text-decoration-none text-dark">Date</a><i class="fas{{ $sortIcon('date') }} ms-1"></i></th>
                        <th><a href="{{ $sortUrl('buyer_name') }}" class="text-decoration-none text-dark">Client</a><i class="fas{{ $sortIcon('buyer_name') }} ms-1"></i></th>
                        <th><a href="{{ $sortUrl('batch') }}" class="text-decoration-none text-dark">Batch</a><i class="fas{{ $sortIcon('batch') }} ms-1"></i></th>
                        <th>Line items</th>
                        <th>Total</th>
                        <th><a href="{{ $sortUrl('amount_paid') }}" class="text-decoration-none text-dark">Received</a><i class="fas{{ $sortIcon('amount_paid') }} ms-1"></i></th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientSales as $sale)
                    <tr>
                        <td>{{ $sale->date->format('M d, Y') }}</td>
                        <td><strong>{{ $sale->buyer_name ?: '—' }}</strong></td>
                        <td>{{ $sale->birdBatch->batch_code ?? 'N/A' }}</td>
                        <td>
                            @foreach($sale->items as $item)
                                <span class="badge bg-light text-dark border me-1 mb-1">
                                    {{ $item->quantity_sold }} {{ strtolower($item->egg_size_label) }} @ ₵{{ number_format($item->price_per_unit, 0) }}
                                    @if($item->payment_status === 'unpaid') <span class="text-danger">(unpaid)</span> @endif
                                </span>
                            @endforeach
                        </td>
                        <td><strong>₵{{ number_format($sale->total_amount, 2) }}</strong></td>
                        <td class="text-success">₵{{ number_format($sale->amount_paid, 2) }}</td>
                        <td class="text-danger">₵{{ number_format($sale->balance, 2) }}</td>
                        <td>
                            @if($sale->payment_status === 'paid')
                                <span class="badge bg-success">Paid</span>
                            @elseif($sale->payment_status === 'partial')
                                <span class="badge bg-warning text-dark">Partial</span>
                            @else
                                <span class="badge bg-danger">Unpaid</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('egg-sales.show', $sale) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('egg-sales.edit', $sale) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('egg-sales.destroy', $sale) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this client sale?');">
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
                            <p class="text-muted">No client egg sales recorded yet</p>
                            <a href="{{ route('egg-sales.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Record First Sale
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clientSales->hasPages())
        <div class="mt-4">
            {{ $clientSales->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

@if($legacySales->count() > 0)
<div class="agri-card">
    <div class="agri-card-header">
        <h3 class="h6 mb-0"><i class="fas fa-history me-2"></i>Other recorded sales (online store / legacy)</h3>
    </div>
    <div class="agri-card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Buyer</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Total</th>
                        <th>Source</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($legacySales as $sale)
                    <tr>
                        <td>{{ $sale->date->format('M d, Y') }}</td>
                        <td>{{ $sale->buyer_name ?? 'N/A' }}</td>
                        <td>{{ number_format($sale->quantity_sold) }}</td>
                        <td>{{ ucfirst($sale->unit_type) }}</td>
                        <td>₵{{ number_format($sale->line_total, 2) }}</td>
                        <td>
                            @if($sale->market_order_id)
                                <span class="badge bg-info">Online order</span>
                            @else
                                <span class="badge bg-secondary">Legacy</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($legacySales->hasPages())
        <div class="mt-3">
            {{ $legacySales->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endif
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
                        @php
                            $oq = request()->except('online_page');
                            $onlineSortUrl = fn ($col) => request()->fullUrlWithQuery(array_merge($oq, ['online_sort' => $col, 'online_direction' => ($onlineSort === $col && $onlineDir === 'asc') ? 'desc' : 'asc', 'online_page' => null]));
                            $onlineSortIcon = fn ($col) => $onlineSort === $col ? ($onlineDir === 'asc' ? ' fa-sort-up' : ' fa-sort-down') : ' fa-sort text-muted';
                        @endphp
                        <th><a href="{{ $onlineSortUrl('order_number') }}" class="text-decoration-none text-dark">Order #</a><i class="fas{{ $onlineSortIcon('order_number') }} ms-1"></i></th>
                        <th><a href="{{ $onlineSortUrl('created_at') }}" class="text-decoration-none text-dark">Date</a><i class="fas{{ $onlineSortIcon('created_at') }} ms-1"></i></th>
                        <th><a href="{{ $onlineSortUrl('customer_name') }}" class="text-decoration-none text-dark">Customer</a><i class="fas{{ $onlineSortIcon('customer_name') }} ms-1"></i></th>
                        <th>Contact</th>
                        <th>Items</th>
                        <th><a href="{{ $onlineSortUrl('total_amount') }}" class="text-decoration-none text-dark">Total</a><i class="fas{{ $onlineSortIcon('total_amount') }} ms-1"></i></th>
                        <th><a href="{{ $onlineSortUrl('status') }}" class="text-decoration-none text-dark">Status</a><i class="fas{{ $onlineSortIcon('status') }} ms-1"></i></th>
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
