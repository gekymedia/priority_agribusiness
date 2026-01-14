@extends('layouts.app')

@section('title', 'Egg Production')

@section('content')
<div class="page-header">
    <h1 class="page-title">Egg Production Records</h1>
    <p class="page-subtitle">Track daily egg production from your layer batches</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('egg-productions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Production Record
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
                        <th>Eggs Collected</th>
                        <th>Cracked/Damaged</th>
                        <th>Used Internal</th>
                        <th>Available</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productions as $production)
                    <tr>
                        <td>{{ $production->date->format('M d, Y') }}</td>
                        <td>{{ $production->birdBatch->batch_code ?? 'N/A' }}</td>
                        <td>{{ $production->birdBatch->farm->name ?? 'N/A' }}</td>
                        <td><strong>{{ number_format($production->eggs_collected) }}</strong></td>
                        <td>{{ number_format($production->cracked_or_damaged) }}</td>
                        <td>{{ number_format($production->eggs_used_internal) }}</td>
                        <td>
                            <span class="badge bg-success">
                                {{ number_format($production->eggs_collected - $production->cracked_or_damaged - $production->eggs_used_internal) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('egg-productions.show', $production) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('egg-productions.edit', $production) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('egg-productions.destroy', $production) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this record?');">
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
                            <p class="text-muted">No egg production records found</p>
                            <a href="{{ route('egg-productions.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add First Record
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($productions->hasPages())
        <div class="mt-4">
            {{ $productions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
