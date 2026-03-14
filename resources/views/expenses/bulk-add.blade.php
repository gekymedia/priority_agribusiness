@extends('layouts.app')

@section('title', 'Bulk Add Expenses')

@section('content')
<div class="page-header">
    <h1 class="page-title">Bulk Add Expenses</h1>
    <p class="page-subtitle">Paste expense lines from your ledger; one line per expense (date, description, amount). Use defaults below for farm, category, and batch.</p>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="agri-card">
    <div class="agri-card-body">
        <form method="POST" action="{{ route('expenses.bulk-add.store') }}">
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
                    <small class="text-muted">Leave as "General Expense" if not tied to a specific batch.</small>
                </div>

                <div class="col-md-6">
                    <label for="category_id" class="form-label">
                        <i class="fas fa-tag me-2"></i>Default Category
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
                    <small class="text-muted">Used for all lines unless you add a 4th column with a category name.</small>
                </div>

                <div class="col-md-6">
                    <label for="default_date" class="form-label">
                        <i class="fas fa-calendar me-2"></i>Default Date
                    </label>
                    <input type="date" name="default_date" id="default_date" class="form-control @error('default_date') is-invalid @enderror" value="{{ old('default_date', date('Y-m-d')) }}" required>
                    @error('default_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Used for lines where date is "—", blank, or "same".</small>
                </div>

                <div class="col-12">
                    <label for="pasted_data" class="form-label">
                        <i class="fas fa-paste me-2"></i>Paste expense lines (one per row)
                    </label>
                    <textarea name="pasted_data" id="pasted_data" class="form-control font-monospace @error('pasted_data') is-invalid @enderror" rows="16" placeholder="21 Jan	Transportation of water gallons	70&#10;—	Bending wire	60&#10;—	Chicken feed	260&#10;—	Feed	260">{{ old('pasted_data') }}</textarea>
                    @error('pasted_data')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="mt-2 small text-muted">
                        <strong>Format:</strong> One line per expense. Columns separated by <strong>tab</strong> or <strong>comma</strong>:<br>
                        <code>date</code> (or — for default date) · <code>description</code> · <code>amount</code> · <code>category</code> (optional)<br>
                        Paste from a spreadsheet or use an AI to convert your ledger: see <code>docs/BULK_EXPENSES_AI_PROMPT.md</code> for a ready-to-use prompt. First line can be a header and will be skipped.
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-file-import me-2"></i>Bulk Add Expenses
                </button>
                <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
                <a href="{{ route('expenses.create') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-plus me-2"></i>Add single expense
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
