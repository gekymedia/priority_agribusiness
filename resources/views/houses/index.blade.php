@extends('layouts.app')

@section('title', 'Houses')

@section('content')
<div class="page-header">
    <h1 class="page-title">Houses / Pens</h1>
    <p class="page-subtitle">Manage housing facilities for your birds and livestock</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('houses.create') }}" class="btn btn-agri">
            <i class="fas fa-plus me-2"></i>Add House
        </a>
    </div>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <div class="table-responsive">
            <table class="table table-hover">
    <thead>
        <tr>
            <th>Farm</th>
            <th>Name</th>
            <th>Capacity</th>
            <th>Type</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
                    @forelse($houses as $house)
        <tr>
                        <td>{{ $house->farm->name ?? 'N/A' }}</td>
                        <td><strong>{{ $house->name }}</strong></td>
                        <td><span class="badge bg-success">{{ number_format($house->capacity) }}</span></td>
                        <td><span class="badge bg-info">{{ ucfirst($house->type) }}</span></td>
            <td>
                            <a href="{{ route('houses.show', $house) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('houses.edit', $house) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('houses.destroy', $house) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this house?');">
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
                        <td colspan="5" class="text-center py-5">
                            <i class="fas fa-home fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No houses registered yet</p>
                            <a href="{{ route('houses.create') }}" class="btn btn-agri">
                                <i class="fas fa-plus me-2"></i>Add First House
                            </a>
                        </td>
                    </tr>
                    @endforelse
    </tbody>
</table>
        </div>

        @if($houses->hasPages())
        <div class="mt-4">
            {{ $houses->links() }}
        </div>
        @endif
    </div>
</div>
@endsection