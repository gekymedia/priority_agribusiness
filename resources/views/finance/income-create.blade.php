@extends('layouts.app')

@section('title', 'Add Income')

@section('content')
<div class="page-header">
    <h1 class="page-title">Add Income</h1>
    <p class="page-subtitle">Record income for Account & Finance (can sync to Priority Bank)</p>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <form method="POST" action="{{ route('finance.income.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="category" class="form-label"><i class="fas fa-tag me-2"></i>Category</label>
                    <input type="text" name="category" id="category" class="form-control @error('category') is-invalid @enderror" value="{{ old('category') }}" required placeholder="e.g. Sales, Grant">
                    @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="amount" class="form-label"><i class="fas fa-coins me-2"></i>Amount (₵)</label>
                    <input type="number" step="0.01" min="0" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required>
                    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="received_on" class="form-label"><i class="fas fa-calendar me-2"></i>Date Received</label>
                    <input type="date" name="received_on" id="received_on" class="form-control @error('received_on') is-invalid @enderror" value="{{ old('received_on', date('Y-m-d')) }}" required>
                    @error('received_on')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="reference" class="form-label"><i class="fas fa-hashtag me-2"></i>Reference</label>
                    <input type="text" name="reference" id="reference" class="form-control @error('reference') is-invalid @enderror" value="{{ old('reference') }}" placeholder="Optional">
                    @error('reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="description" class="form-label"><i class="fas fa-align-left me-2"></i>Description</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="2">{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="external_transaction_id" class="form-label"><i class="fas fa-link me-2"></i>External Transaction ID</label>
                    <input type="text" name="external_transaction_id" id="external_transaction_id" class="form-control @error('external_transaction_id') is-invalid @enderror" value="{{ old('external_transaction_id') }}" placeholder="Leave blank to auto-generate on sync">
                    @error('external_transaction_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Income</button>
                <a href="{{ route('finance.income.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
