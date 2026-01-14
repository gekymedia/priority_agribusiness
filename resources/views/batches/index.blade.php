@extends('layouts.app')

@section('title', 'Bird Batches')

@section('content')
<div class="page-header">
    <h1 class="page-title">Bird Batches</h1>
    <p class="page-subtitle">Track and manage your poultry batches</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('batches.create') }}" class="btn btn-agri">
            <i class="fas fa-plus me-2"></i>Add Batch
        </a>
    </div>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <div class="table-responsive">
            <table class="table table-hover">
    <thead>
        <tr>
            <th>Batch Code</th>
            <th>Farm</th>
            <th>House</th>
            <th>Purpose</th>
            <th>Arrival Date</th>
            <th>Quantity</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
                    @forelse($batches as $batch)
        <tr>
                        <td><strong>{{ $batch->batch_code }}</strong></td>
                        <td>{{ $batch->farm->name ?? 'N/A' }}</td>
                        <td>{{ $batch->house->name ?? 'N/A' }}</td>
                        <td><span class="badge bg-primary">{{ ucfirst($batch->purpose) }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($batch->arrival_date)->format('M d, Y') }}</td>
                        <td><span class="badge bg-success">{{ number_format($batch->quantity_arrived) }}</span></td>
                        <td>
                            @if($batch->status == 'active')
                                <span class="badge bg-success">{{ ucfirst($batch->status) }}</span>
                            @elseif($batch->status == 'completed')
                                <span class="badge bg-secondary">{{ ucfirst($batch->status) }}</span>
                            @else
                                <span class="badge bg-warning">{{ ucfirst($batch->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('batches.show', $batch) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('batches.edit', $batch) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('batches.destroy', $batch) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this batch?');">
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
                            <i class="fas fa-dove fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No bird batches registered yet</p>
                            <a href="{{ route('batches.create') }}" class="btn btn-agri">
                                <i class="fas fa-plus me-2"></i>Add First Batch
                            </a>
                        </td>
                    </tr>
                    @endforelse
    </tbody>
</table>
        </div>

        @if($batches->hasPages())
        <div class="mt-4">
            {{ $batches->links() }}
        </div>
        @endif
    </div>
</div>
@endsection