@extends('layouts.app')

@section('title', 'View Egg Sale')

@section('content')
<div class="page-header">
    <h1 class="page-title">Egg Sale — {{ $clientSale->buyer_name ?: 'Client #' . $clientSale->id }}</h1>
    <p class="page-subtitle">{{ $clientSale->date->format('F d, Y') }}</p>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="agri-card mb-4">
            <div class="agri-card-header">
                <h3><i class="fas fa-user me-2"></i>Client Summary</h3>
            </div>
            <div class="agri-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted">Buyer</label>
                        <p class="h5">{{ $clientSale->buyer_name ?: 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted">Contact</label>
                        <p class="h5">{{ $clientSale->buyer_contact ?: 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted">Batch</label>
                        <p class="h6">{{ $clientSale->birdBatch->batch_code ?? 'N/A' }} — {{ $clientSale->birdBatch->farm->name ?? '' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted">Payment status</label>
                        <p>
                            @if($clientSale->payment_status === 'paid')
                                <span class="badge bg-success">{{ $clientSale->payment_status_label }}</span>
                            @elseif($clientSale->payment_status === 'partial')
                                <span class="badge bg-warning text-dark">{{ $clientSale->payment_status_label }}</span>
                            @else
                                <span class="badge bg-danger">{{ $clientSale->payment_status_label }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted">Total purchases</label>
                        <p class="h5">₵{{ number_format($clientSale->total_amount, 2) }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted">Amount received</label>
                        <p class="h5 text-success">₵{{ number_format($clientSale->amount_paid, 2) }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted">Balance outstanding</label>
                        <p class="h5 text-danger">₵{{ number_format($clientSale->balance, 2) }}</p>
                    </div>
                    @if($clientSale->notes)
                    <div class="col-12">
                        <label class="text-muted">Notes</label>
                        <p>{{ $clientSale->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="agri-card">
            <div class="agri-card-header">
                <h3><i class="fas fa-egg me-2"></i>Line Items</h3>
            </div>
            <div class="agri-card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Size</th>
                                <th>Qty</th>
                                <th>Price/egg</th>
                                <th>Line total</th>
                                <th>Payment</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clientSale->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><span class="badge bg-secondary">{{ $item->egg_size_label }}</span></td>
                                <td><strong>{{ number_format($item->quantity_sold) }}</strong></td>
                                <td>₵{{ number_format($item->price_per_unit, 2) }}</td>
                                <td><strong>₵{{ number_format($item->line_total, 2) }}</strong></td>
                                <td>
                                    @if($item->payment_status === 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @else
                                        <span class="badge bg-danger">Unpaid</span>
                                    @endif
                                </td>
                                <td>{{ $item->notes ?: '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="2"><strong>Totals</strong></td>
                                <td><strong>{{ number_format($clientSale->items->sum('quantity_sold')) }} eggs</strong></td>
                                <td></td>
                                <td><strong>₵{{ number_format($clientSale->total_amount, 2) }}</strong></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="agri-card">
            <div class="agri-card-body">
                <h5 class="mb-3">Actions</h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('egg-sales.edit', $clientSale) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit Sale
                    </a>
                    <form action="{{ route('egg-sales.destroy', $clientSale) }}" method="POST" onsubmit="return confirm('Delete this client sale and all line items?');">
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
