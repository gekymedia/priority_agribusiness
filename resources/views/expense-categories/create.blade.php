@extends('layouts.app')

@section('title', 'Add Expense Category')

@section('content')
<div class="page-header">
    <h1 class="page-title">Add Expense Category</h1>
    <p class="page-subtitle">Create a new expense category</p>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <form method="POST" action="{{ route('expense-categories.store') }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">
                        <i class="fas fa-tag me-2"></i>Category Name
                    </label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="type" class="form-label">
                        <i class="fas fa-filter me-2"></i>Type
                    </label>
                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="poultry" {{ old('type') == 'poultry' ? 'selected' : '' }}>Poultry</option>
                        <option value="crop" {{ old('type') == 'crop' ? 'selected' : '' }}>Crop</option>
                        <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>General</option>
                    </select>
                    @error('type')
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

                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active (Category will be available for use)
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Create Category
                </button>
                <a href="{{ route('expense-categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
