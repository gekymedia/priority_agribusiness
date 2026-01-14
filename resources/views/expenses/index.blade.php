@extends('layouts.app')

@section('title', 'Expenses')

@section('content')
<div class="page-header">
    <h1 class="page-title">Expenses</h1>
    <p class="page-subtitle">Track all farm expenses</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary me-2">
            <i class="fas fa-plus me-2"></i>Add Expense
        </a>
        <a href="{{ route('expense-categories.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-tags me-2"></i>Manage Categories
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
                        <th>Farm</th>
                        <th>Batch</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                    <tr>
                        <td>{{ $expense->date->format('M d, Y') }}</td>
                        <td>{{ $expense->farm->name ?? 'N/A' }}</td>
                        <td>{{ $expense->birdBatch->batch_code ?? 'General' }}</td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                {{ $expense->category->name ?? $expense->category ?? 'N/A' }}
                            </span>
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit($expense->description ?? 'N/A', 30) }}</td>
                        <td><strong class="text-danger">₵{{ number_format($expense->amount, 2) }}</strong></td>
                        <td>
                            <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this expense?');">
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
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No expenses recorded</p>
                            <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add First Expense
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
        <div class="mt-4">
            {{ $expenses->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
