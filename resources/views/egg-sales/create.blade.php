@extends('layouts.app')

@section('title', 'Record Egg Sale')

@section('content')
<div class="page-header">
    <h1 class="page-title">Record Egg Sale @include('egg-sales._stock_balance')</h1>
    <p class="page-subtitle">Record a client sale with egg sizes and payment status</p>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <form method="POST" action="{{ route('egg-sales.store') }}">
            @csrf
            @include('egg-sales._form', ['batches' => $batches])

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Record Sale
                </button>
                <a href="{{ route('egg-sales.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
                <a href="{{ route('egg-sales.bulk-import') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-file-import me-2"></i>Bulk import instead
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
