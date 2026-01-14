@extends('layouts.app')

@section('title', 'Add Expense')

@section('content')
<div class="page-header">
    <h1 class="page-title">Add Expense</h1>
    <p class="page-subtitle">Record a new farm expense</p>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <form method="POST" action="{{ route('expenses.store') }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="farm_id" class="form-label">
                        <i class="fas fa-tractor me-2"></i>Farm
                    </label>
                    <select name="farm_id" id="farm_id" class="form-select @error('farm_id') is-invalid @enderror" required>
                        <option value="">Select Farm</option>
                        @foreach($farms as $farm)
                            <option value="{{ $farm->id }}" {{ old('farm_id') == $farm->id ? 'selected' : '' }}>
                                {{ $farm->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('farm_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="bird_batch_id" class="form-label">
                        <i class="fas fa-dove me-2"></i>Bird Batch (Optional)
                    </label>
                    <select name="bird_batch_id" id="bird_batch_id" class="form-select @error('bird_batch_id') is-invalid @enderror">
                        <option value="">General Expense</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}" {{ old('bird_batch_id') == $batch->id ? 'selected' : '' }}>
                                {{ $batch->batch_code }} - {{ $batch->farm->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('bird_batch_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="category_id" class="form-label">
                        <i class="fas fa-tag me-2"></i>Category
                    </label>
                    <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }} ({{ ucfirst($category->type) }})
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Don't see your category? <a href="{{ route('expense-categories.create') }}">Create one</a></small>
                </div>

                <div class="col-md-6">
                    <label for="date" class="form-label">
                        <i class="fas fa-calendar me-2"></i>Expense Date
                    </label>
                    <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', date('Y-m-d')) }}" required>
                    @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="amount" class="form-label">
                        <i class="fas fa-money-bill me-2"></i>Amount (₵)
                    </label>
                    <input type="number" name="amount" id="amount" step="0.01" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" min="0" required>
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="description" class="form-label">
                        <i class="fas fa-sticky-note me-2"></i>Description
                    </label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Save Expense
                </button>
                <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
