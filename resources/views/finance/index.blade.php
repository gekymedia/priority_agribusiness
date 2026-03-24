@extends('layouts.app')

@section('title', 'Finance Ledger')

@section('content')
<div class="page-header">
    <h1 class="page-title">Finance Ledger</h1>
    <p class="page-subtitle">Income and expenses combined in one place</p>
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
    <div class="d-flex gap-2">
        <a href="{{ route('finance.income.create') }}" class="btn btn-primary">
            <i class="fas fa-arrow-down me-2"></i>Add Income
        </a>
        <a href="{{ route('expenses.create') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-up me-2"></i>Add Expense
        </a>
    </div>
    <form method="GET" class="d-flex gap-2">
        <input type="date" name="from" value="{{ request('from') }}" class="form-control">
        <input type="date" name="to" value="{{ request('to') }}" class="form-control">
        <button class="btn btn-secondary" type="submit">Filter</button>
        <a href="{{ route('finance.index') }}" class="btn btn-light">Clear</a>
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="agri-card">
            <div class="agri-card-body">
                <div class="text-muted small"><i class="fas fa-arrow-down text-success me-1"></i>Total Income</div>
                <h4 class="mb-0 text-success">₵{{ number_format($incomeTotal ?? 0, 2) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="agri-card">
            <div class="agri-card-body">
                <div class="text-muted small"><i class="fas fa-arrow-up text-danger me-1"></i>Total Expenses</div>
                <h4 class="mb-0 text-danger">₵{{ number_format($expenseTotal ?? 0, 2) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="agri-card">
            <div class="agri-card-body">
                <div class="text-muted small"><i class="fas fa-balance-scale me-1"></i>Balance</div>
                <h4 class="mb-0 {{ ($balance ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">₵{{ number_format($balance ?? 0, 2) }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Source</th>
                        <th>Amount</th>
                        <th>External ID</th>
                        <th>Sync</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ledger as $row)
                        <tr>
                            <td>{{ $row->date?->format('M d, Y') }}</td>
                            <td>
                                @if($row->entry_type === 'income')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fas fa-arrow-down me-1"></i>Income</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="fas fa-arrow-up me-1"></i>Expense</span>
                                @endif
                            </td>
                            <td>{{ $row->category }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($row->description ?? '—', 50) }}</td>
                            <td class="text-muted small">{{ $row->source }}</td>
                            <td>
                                <strong class="{{ $row->entry_type === 'income' ? 'text-success' : 'text-danger' }}">
                                    {{ $row->entry_type === 'income' ? '+' : '-' }}₵{{ number_format($row->amount, 2) }}
                                </strong>
                            </td>
                            <td class="text-muted small">{{ $row->external_transaction_id ?? '—' }}</td>
                            <td>
                                @if($row->can_sync && $row->sync_route)
                                    <form action="{{ $row->sync_route }}" method="POST" class="d-inline" data-sync-form>
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Sync with Priority Bank">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-light text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-wallet fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No finance records found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($ledger->hasPages())
            <div class="mt-4">{{ $ledger->withQueryString()->links() }}</div>
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
