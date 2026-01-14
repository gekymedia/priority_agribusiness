@extends('layouts.app')

@section('title', 'View Expense')

@section('content')
<div class="page-header">
    <h1 class="page-title">Expense Details</h1>
    <p class="page-subtitle">Expense information</p>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="agri-card">
            <div class="agri-card-header">
                <h3><i class="fas fa-money-bill me-2"></i>Expense Information</h3>
            </div>
            <div class="agri-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted">Date</label>
                        <p class="h5">{{ $expense->date->format('F d, Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted">Farm</label>
                        <p class="h5">{{ $expense->farm->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted">Bird Batch</label>
                        <p class="h5">{{ $expense->birdBatch->batch_code ?? 'General Expense' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted">Category</label>
                        <p>
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                {{ $expense->category->name ?? $expense->category ?? 'N/A' }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted">Amount</label>
                        <p class="h4 text-danger">
                            <strong>₵{{ number_format($expense->amount, 2) }}</strong>
                        </p>
                    </div>
                    @if($expense->description)
                    <div class="col-12">
                        <label class="text-muted">Description</label>
                        <p>{{ $expense->description }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="agri-card">
            <div class="agri-card-body">
                <h5 class="mb-3">Actions</h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit Expense
                    </a>
                    <form action="{{ route('expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('Delete this expense?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash me-2"></i>Delete
                        </button>
                    </form>
                    <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
