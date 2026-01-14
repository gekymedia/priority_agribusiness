@extends('layouts.app')

@section('title', 'Edit Expense Category')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Expense Category</h1>
    <p class="page-subtitle">Update category information</p>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <form method="POST" action="{{ route('expense-categories.update', $expenseCategory) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">
                        <i class="fas fa-tag me-2"></i>Category Name
                    </label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $expenseCategory->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="type" class="form-label">
                        <i class="fas fa-filter me-2"></i>Type
                    </label>
                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="poultry" {{ old('type', $expenseCategory->type) == 'poultry' ? 'selected' : '' }}>Poultry</option>
                        <option value="crop" {{ old('type', $expenseCategory->type) == 'crop' ? 'selected' : '' }}>Crop</option>
                        <option value="general" {{ old('type', $expenseCategory->type) == 'general' ? 'selected' : '' }}>General</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="description" class="form-label">
                        <i class="fas fa-sticky-note me-2"></i>Description
                    </label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $expenseCategory->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $expenseCategory->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active (Category will be available for use)
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Category
                </button>
                <a href="{{ route('expense-categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
