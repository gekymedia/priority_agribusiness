@extends('layouts.app')

@section('title', 'Plantings')

@section('content')
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
            <th>Field</th>
            <th>Crop</th>
            <th>Planting Date</th>
            <th>Expected Harvest</th>
            <th>Status</th>
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