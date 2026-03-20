@extends('layouts.app')

@section('title', 'Income')

@section('content')
<div class="page-header">
    <h1 class="page-title">Income</h1>
    <p class="page-subtitle">Account & Finance – income records linked with Priority Bank</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('finance.income.create') }}" class="btn btn-primary me-2">
            <i class="fas fa-plus me-2"></i>Add Income
        </a>
    </div>
</div>

<div class="agri-card mb-4">
    <div class="agri-card-body">
        <div class="text-muted small">Total (filtered)</div>
        <h4 class="mb-0">₵{{ number_format($total ?? 0, 2) }}</h4>
    </div>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>External ID</th>
                        <th>Sync</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incomes as $income)
                    <tr>
                        <td>{{ $income->received_on?->format('M d, Y') }}</td>
                        <td>{{ $income->category }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($income->description ?? '—', 40) }}</td>
                        <td><strong class="text-success">₵{{ number_format($income->amount, 2) }}</strong></td>
                        <td class="text-muted small">{{ $income->external_transaction_id ?? '—' }}</td>
                        <td>
                            @if($income->is_manual)
                                <form action="{{ route('finance.income.sync', $income->id) }}" method="POST" class="d-inline" data-sync-form>
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary" title="Sync with Priority Bank">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </form>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    Synced via sale
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-arrow-down-to-line fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No income records</p>
                            <a href="{{ route('finance.income.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add Income
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($incomes->hasPages())
            <div class="mt-4">{{ $incomes->withQueryString()->links() }}</div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('[data-sync-form]').forEach(function(f) {
    f.addEventListener('submit', function() {
        var btn = this.querySelector('button[type="submit"]');
        if (btn) { btn.disabled = true; btn.querySelector('i').classList.add('fa-spin'); }
    });
});
</script>
@endpush
@endsection
