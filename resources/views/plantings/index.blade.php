@extends('layouts.app')

@section('title', 'Plantings')

@section('content')
@php
    $sort = $sort ?? 'planting_date';
    $direction = $direction ?? 'desc';
@endphp
<div class="page-header">
    <h1 class="page-title">Plantings</h1>
    <p class="page-subtitle">Track crop planting activities and schedules</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('plantings.create') }}" class="btn btn-agri">
            <i class="fas fa-plus me-2"></i>Add Planting
        </a>
    </div>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <div class="table-responsive">
            <table class="table table-hover">
    <thead>
        <tr>
            @php
                $sortUrl = fn ($col) => request()->fullUrlWithQuery(['sort' => $col, 'direction' => ($sort === $col && $direction === 'asc') ? 'desc' : 'asc', 'page' => null]);
                $sortIcon = fn ($col) => $sort === $col ? ($direction === 'asc' ? ' fa-sort-up' : ' fa-sort-down') : ' fa-sort text-muted';
            @endphp
            <th><a href="{{ $sortUrl('field') }}" class="text-decoration-none text-dark">Field</a><i class="fas{{ $sortIcon('field') }} ms-1"></i></th>
            <th><a href="{{ $sortUrl('crop_name') }}" class="text-decoration-none text-dark">Crop</a><i class="fas{{ $sortIcon('crop_name') }} ms-1"></i></th>
            <th><a href="{{ $sortUrl('planting_date') }}" class="text-decoration-none text-dark">Planting Date</a><i class="fas{{ $sortIcon('planting_date') }} ms-1"></i></th>
            <th><a href="{{ $sortUrl('expected_harvest_date') }}" class="text-decoration-none text-dark">Expected Harvest</a><i class="fas{{ $sortIcon('expected_harvest_date') }} ms-1"></i></th>
            <th><a href="{{ $sortUrl('status') }}" class="text-decoration-none text-dark">Status</a><i class="fas{{ $sortIcon('status') }} ms-1"></i></th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
                    @forelse($plantings as $planting)
        <tr>
                        <td>{{ $planting->field->name ?? 'N/A' }}</td>
                        <td><strong>{{ $planting->crop_name }}</strong></td>
                        <td>{{ \Carbon\Carbon::parse($planting->planting_date)->format('M d, Y') }}</td>
                        <td>{{ $planting->expected_harvest_date ? \Carbon\Carbon::parse($planting->expected_harvest_date)->format('M d, Y') : '-' }}</td>
                        <td>
                            @if($planting->status == 'active')
                                <span class="badge bg-success">{{ ucfirst($planting->status) }}</span>
                            @elseif($planting->status == 'harvested')
                                <span class="badge bg-secondary">{{ ucfirst($planting->status) }}</span>
                            @else
                                <span class="badge bg-warning">{{ ucfirst($planting->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('plantings.show', $planting) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('plantings.edit', $planting) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('plantings.destroy', $planting) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this planting?');">
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
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-leaf fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No plantings recorded yet</p>
                            <a href="{{ route('plantings.create') }}" class="btn btn-agri">
                                <i class="fas fa-plus me-2"></i>Add First Planting
                            </a>
                        </td>
                    </tr>
                    @endforelse
    </tbody>
</table>
        </div>

        @if($plantings->hasPages())
        <div class="mt-4">
            {{ $plantings->links() }}
        </div>
        @endif
    </div>
</div>
@endsection