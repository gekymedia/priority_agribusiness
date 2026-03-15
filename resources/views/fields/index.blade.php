@extends('layouts.app')

@section('title', 'Fields')

@section('content')
@php
    $sort = $sort ?? 'name';
    $direction = $direction ?? 'asc';
@endphp
<div class="page-header">
    <h1 class="page-title">Fields / Plots</h1>
    <p class="page-subtitle">Manage your agricultural land areas</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('fields.create') }}" class="btn btn-agri">
            <i class="fas fa-plus me-2"></i>Add Field
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
            <th><a href="{{ $sortUrl('farm') }}" class="text-decoration-none text-dark">Farm</a><i class="fas{{ $sortIcon('farm') }} ms-1"></i></th>
            <th><a href="{{ $sortUrl('name') }}" class="text-decoration-none text-dark">Name</a><i class="fas{{ $sortIcon('name') }} ms-1"></i></th>
            <th><a href="{{ $sortUrl('size') }}" class="text-decoration-none text-dark">Size</a><i class="fas{{ $sortIcon('size') }} ms-1"></i></th>
            <th><a href="{{ $sortUrl('soil_type') }}" class="text-decoration-none text-dark">Soil Type</a><i class="fas{{ $sortIcon('soil_type') }} ms-1"></i></th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
                    @forelse($fields as $field)
        <tr>
                        <td>{{ $field->farm->name ?? 'N/A' }}</td>
                        <td><strong>{{ $field->name }}</strong></td>
                        <td><span class="badge bg-success">{{ $field->size }}</span></td>
                        <td><span class="badge bg-info">{{ ucfirst($field->soil_type) }}</span></td>
            <td>
                            <a href="{{ route('fields.show', $field) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('fields.edit', $field) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('fields.destroy', $field) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this field?');">
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
                            <i class="fas fa-border-all fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No fields registered yet</p>
                            <a href="{{ route('fields.create') }}" class="btn btn-agri">
                                <i class="fas fa-plus me-2"></i>Add First Field
                            </a>
                        </td>
                    </tr>
                    @endforelse
    </tbody>
</table>
        </div>

        @if($fields->hasPages())
        <div class="mt-4">
            {{ $fields->links() }}
        </div>
        @endif
    </div>
</div>
@endsection