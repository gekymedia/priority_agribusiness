@extends('layouts.app')

@section('title', 'Farms')

@section('content')
<div class="page-header">
    <h1 class="page-title">Farms</h1>
    <p class="page-subtitle">Manage your agricultural farm locations</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('farms.create') }}" class="btn btn-agri">
            <i class="fas fa-plus me-2"></i>Add Farm
        </a>
    </div>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <div class="table-responsive">
            <table class="table table-hover">
    <thead>
        <tr>
            <th>Name</th>
            <th>Location</th>
            <th>Type</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
                    @forelse($farms as $farm)
        <tr>
                        <td><strong>{{ $farm->name }}</strong></td>
            <td>{{ $farm->location }}</td>
                        <td><span class="badge bg-primary">{{ ucfirst($farm->farm_type) }}</span></td>
            <td>
                            <a href="{{ route('farms.show', $farm) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('farms.edit', $farm) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('farms.destroy', $farm) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this farm?');">
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
                        <td colspan="4" class="text-center py-5">
                            <i class="fas fa-tractor fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No farms registered yet</p>
                            <a href="{{ route('farms.create') }}" class="btn btn-agri">
                                <i class="fas fa-plus me-2"></i>Add First Farm
                            </a>
                        </td>
                    </tr>
                    @endforelse
    </tbody>
</table>
        </div>

        @if($farms->hasPages())
        <div class="mt-4">
            {{ $farms->links() }}
        </div>
        @endif
    </div>
</div>
@endsection