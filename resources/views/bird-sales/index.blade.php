@extends('layouts.app')

@section('title', 'Bird Sales')

@section('content')
<div class="page-header">
    <h1 class="page-title">Bird Sales</h1>
    <p class="page-subtitle">Track all bird/chicken sales transactions</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('bird-sales.create') }}" class="btn btn-primary">
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
                        <th>Quantity Sold</th>
                        <th>Price/Bird</th>
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
                        <td>₵{{ number_format($sale->price_per_bird, 2) }}</td>
                        <td><strong class="text-success">₵{{ number_format($sale->quantity_sold * $sale->price_per_bird, 2) }}</strong></td>
                        <td>{{ $sale->buyer_name ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('bird-sales.show', $sale) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('bird-sales.edit', $sale) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('bird-sales.destroy', $sale) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this sale?');">
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
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No bird sales recorded</p>
                            <a href="{{ route('bird-sales.create') }}" class="btn btn-primary">
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
            {{ $sales->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
