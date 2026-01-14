@extends('layouts.app')

@section('title', 'Expense Categories')

@section('content')
<div class="page-header">
    <h1 class="page-title">Expense Categories</h1>
    <p class="page-subtitle">Manage expense categories for better organization</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('expense-categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Category
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
                        <th>Type</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td><strong>{{ $category->name }}</strong></td>
                        <td>
                            <span class="badge bg-{{ $category->type == 'poultry' ? 'primary' : ($category->type == 'crop' ? 'success' : 'info') }} bg-opacity-10 text-{{ $category->type == 'poultry' ? 'primary' : ($category->type == 'crop' ? 'success' : 'info') }}">
                                {{ ucfirst($category->type) }}
                            </span>
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit($category->description ?? 'N/A', 50) }}</td>
                        <td>
                            @if($category->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('expense-categories.edit', $category) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('expense-categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?');">
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
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No expense categories found</p>
                            <a href="{{ route('expense-categories.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create First Category
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
        <div class="mt-4">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
