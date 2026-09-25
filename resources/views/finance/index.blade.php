@extends('layouts.app')

@section('title', 'Finance Ledger')

@section('content')
<style>
    .finance-page .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 1.5rem;
        flex-wrap: wrap;
        margin-bottom: 1.75rem;
    }
    .finance-page .page-header-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
    }
    .finance-stat {
        background: #fff;
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        padding: 1.35rem 1.4rem;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .finance-stat::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 4px;
        background: var(--stat-accent, var(--primary));
    }
    .finance-stat-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 0.85rem;
    }
    .finance-stat-label {
        color: var(--text-light);
        font-size: 0.85rem;
        font-weight: 600;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }
    .finance-stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        background: var(--stat-accent, var(--primary));
        flex-shrink: 0;
    }
    .finance-stat-value {
        font-size: 1.75rem;
        font-weight: 800;
        line-height: 1.1;
        margin: 0;
    }
    .finance-stat-note {
        margin-top: 0.35rem;
        color: var(--text-light);
        font-size: 0.85rem;
    }
    .finance-toolbar {
        background: #fff;
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        padding: 1.15rem 1.25rem;
        margin-bottom: 1.25rem;
    }
    .finance-type-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .finance-type-pill {
        border: 1px solid #d9e3d9;
        background: #f7faf7;
        color: #456245;
        border-radius: 999px;
        padding: 0.45rem 0.95rem;
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .finance-type-pill:hover {
        color: var(--primary-dark);
        border-color: var(--primary-light);
        background: #eef7ee;
    }
    .finance-type-pill.active {
        background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 8px 18px rgba(46, 125, 50, 0.25);
    }
    .finance-date-form {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
        align-items: end;
    }
    .finance-date-form .form-label {
        margin-bottom: 0.35rem;
        font-size: 0.8rem;
    }
    .finance-date-form .form-control {
        min-width: 150px;
        padding: 0.55rem 0.8rem;
        border-radius: 10px;
    }
    .finance-ledger-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .finance-ledger-card:hover {
        transform: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }
    .finance-ledger-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
        padding: 1.1rem 1.35rem;
        border-bottom: 1px solid rgba(0,0,0,0.06);
        background: linear-gradient(180deg, #fbfdfb 0%, #fff 100%);
    }
    .finance-ledger-header h2 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
    }
    .finance-ledger-header p {
        margin: 0.2rem 0 0;
        color: var(--text-light);
        font-size: 0.9rem;
    }
    .finance-table {
        margin: 0;
    }
    .finance-table thead th {
        background: #f4f8f4;
        color: #3d5a40;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-weight: 700;
        border-bottom: 1px solid #e3ece3;
        white-space: nowrap;
        padding: 0.85rem 1rem;
    }
    .finance-table tbody td {
        padding: 0.95rem 1rem;
        border-color: #f0f2f0;
        vertical-align: middle;
    }
    .finance-table tbody tr:hover {
        background: #f8fbf8;
    }
    .finance-amount {
        font-weight: 700;
        white-space: nowrap;
    }
    .finance-empty {
        padding: 3.5rem 1.5rem;
        text-align: center;
    }
    .finance-empty-icon {
        width: 72px;
        height: 72px;
        margin: 0 auto 1rem;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eef7ee;
        color: var(--primary);
        font-size: 1.75rem;
    }
    .finance-sync-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border-radius: 999px;
        padding: 0.28rem 0.7rem;
        font-size: 0.78rem;
        font-weight: 600;
    }
    .finance-sync-chip.synced {
        background: #e8f5e9;
        color: #2e7d32;
    }
    .finance-sync-chip.pending {
        background: #fff8e1;
        color: #ef6c00;
    }
    .finance-pending-strip .agri-card {
        border: 1px solid rgba(0,0,0,0.05);
    }
    .finance-pending-strip .agri-card:hover {
        transform: none;
    }
    #exportPdfModal .modal-content {
        border: 0;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 24px 60px rgba(0,0,0,0.18);
    }
    #exportPdfModal .modal-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
        color: #fff;
        border: 0;
        padding: 1.15rem 1.4rem;
    }
    #exportPdfModal .modal-header .btn-close {
        filter: invert(1);
    }
    #exportPdfModal .modal-body {
        padding: 1.4rem;
    }
    #exportPdfModal .export-type-options {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.75rem;
    }
    #exportPdfModal .export-type-option {
        border: 2px solid #e3ece3;
        border-radius: 14px;
        padding: 0.9rem 0.75rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #fafcfa;
        height: 100%;
    }
    #exportPdfModal .export-type-option:hover {
        border-color: var(--primary-light);
    }
    #exportPdfModal .btn-check:checked + .export-type-option {
        border-color: var(--primary);
        background: #eef7ee;
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.12);
    }
    #exportPdfModal .export-type-option i {
        display: block;
        font-size: 1.2rem;
        margin-bottom: 0.4rem;
        color: var(--primary);
    }
    #exportPdfModal .export-type-option strong {
        display: block;
        font-size: 0.92rem;
    }
    #exportPdfModal .export-type-option span {
        display: block;
        color: var(--text-light);
        font-size: 0.78rem;
        margin-top: 0.15rem;
    }
    @media (max-width: 767.98px) {
        .finance-stat-value { font-size: 1.4rem; }
        #exportPdfModal .export-type-options { grid-template-columns: 1fr; }
    }
</style>

<div class="finance-page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Finance Ledger</h1>
            <p class="page-subtitle mb-0">Income and expenses combined in one place</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('finance.income.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Income
            </a>
            <a href="{{ route('expenses.create') }}" class="btn btn-outline-primary">
                <i class="fas fa-receipt me-2"></i>Add Expense
            </a>
            <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#exportPdfModal">
                <i class="fas fa-file-pdf me-2"></i>Export PDF
            </button>
            @if($bankConfigured ?? false)
                <form action="{{ route('finance.sync-all') }}" method="POST" class="d-inline" data-sync-form
                      onsubmit="return confirm('Sync all unsynced records{{ ($pendingSync['total'] ?? 0) > 0 ? ' (' . $pendingSync['total'] . ' pending)' : '' }} to Priority Bank?');">
                    @csrf
                    @if(request('from'))<input type="hidden" name="from" value="{{ request('from') }}">@endif
                    @if(request('to'))<input type="hidden" name="to" value="{{ request('to') }}">@endif
                    @if(request('type'))<input type="hidden" name="type" value="{{ request('type') }}">@endif
                    <button type="submit" class="btn btn-success" {{ ($pendingSync['total'] ?? 0) === 0 ? 'disabled' : '' }}>
                        <i class="fas fa-cloud-upload-alt me-2"></i>Sync All
                        @if(($pendingSync['total'] ?? 0) > 0)
                            <span class="badge bg-light text-success ms-1">{{ $pendingSync['total'] }}</span>
                        @endif
                    </button>
                </form>
                <form action="{{ route('finance.reconcile') }}" method="POST" class="d-inline" data-sync-form
                      onsubmit="return confirm('Match pending records to Priority Bank and restore external IDs? This fixes records that were synced before IDs were stored locally. It will not create duplicate bank entries.');">
                    @csrf
                    @if(request('from'))<input type="hidden" name="from" value="{{ request('from') }}">@endif
                    @if(request('to'))<input type="hidden" name="to" value="{{ request('to') }}">@endif
                    @if(request('type'))<input type="hidden" name="type" value="{{ request('type') }}">@endif
                    <button type="submit" class="btn btn-outline-success" {{ ($pendingSync['total'] ?? 0) === 0 ? 'disabled' : '' }} title="Fix records already in the bank but missing External ID">
                        <i class="fas fa-link me-2"></i>Reconcile
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(!($bankConfigured ?? false))
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Priority Bank is not configured. Add your API URL and token under <a href="{{ route('settings.index') }}">Settings → Priority Bank</a> to sync records.
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="finance-stat" style="--stat-accent: #2e7d32;">
                <div class="finance-stat-top">
                    <div class="finance-stat-label">Total Income</div>
                    <div class="finance-stat-icon"><i class="fas fa-arrow-down"></i></div>
                </div>
                <p class="finance-stat-value text-success">₵{{ number_format($incomeTotal ?? 0, 2) }}</p>
                <div class="finance-stat-note">Money coming in</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="finance-stat" style="--stat-accent: #e53935;">
                <div class="finance-stat-top">
                    <div class="finance-stat-label">Total Expenses</div>
                    <div class="finance-stat-icon"><i class="fas fa-arrow-up"></i></div>
                </div>
                <p class="finance-stat-value text-danger">₵{{ number_format($expenseTotal ?? 0, 2) }}</p>
                <div class="finance-stat-note">Money going out</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="finance-stat" style="--stat-accent: {{ ($balance ?? 0) >= 0 ? '#00897b' : '#fb8c00' }};">
                <div class="finance-stat-top">
                    <div class="finance-stat-label">Balance</div>
                    <div class="finance-stat-icon"><i class="fas fa-scale-balanced"></i></div>
                </div>
                <p class="finance-stat-value {{ ($balance ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">₵{{ number_format($balance ?? 0, 2) }}</p>
                <div class="finance-stat-note">{{ number_format($recordCount ?? 0) }} record{{ ($recordCount ?? 0) === 1 ? '' : 's' }} in view</div>
            </div>
        </div>
    </div>

    @if($bankConfigured ?? false)
        <div class="row g-3 mb-4 finance-pending-strip">
            <div class="col-md-4">
                <div class="agri-card">
                    <div class="agri-card-body py-3">
                        <div class="text-muted small">Pending income sync</div>
                        <h5 class="mb-0 text-success">{{ number_format($pendingSync['income'] ?? 0) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="agri-card">
                    <div class="agri-card-body py-3">
                        <div class="text-muted small">Pending expense sync</div>
                        <h5 class="mb-0 text-danger">{{ number_format($pendingSync['expense'] ?? 0) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="agri-card">
                    <div class="agri-card-body py-3">
                        <div class="text-muted small">Total pending bank sync</div>
                        <h5 class="mb-0">{{ number_format($pendingSync['total'] ?? 0) }}</h5>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="finance-toolbar">
        <div class="row g-3 align-items-end">
            <div class="col-lg-5">
                <div class="form-label mb-2">Show</div>
                <div class="finance-type-pills">
                    @php
                        $baseQuery = request()->except('page');
                    @endphp
                    <a href="{{ route('finance.index', array_merge($baseQuery, ['type' => 'all'])) }}"
                       class="finance-type-pill {{ ($type ?? 'all') === 'all' ? 'active' : '' }}">All</a>
                    <a href="{{ route('finance.index', array_merge($baseQuery, ['type' => 'income'])) }}"
                       class="finance-type-pill {{ ($type ?? 'all') === 'income' ? 'active' : '' }}">Income</a>
                    <a href="{{ route('finance.index', array_merge($baseQuery, ['type' => 'expense'])) }}"
                       class="finance-type-pill {{ ($type ?? 'all') === 'expense' ? 'active' : '' }}">Expenses</a>
                </div>
            </div>
            <div class="col-lg-7">
                <form method="GET" class="finance-date-form justify-content-lg-end">
                    <input type="hidden" name="type" value="{{ $type ?? 'all' }}">
                    <div>
                        <label class="form-label" for="from">From</label>
                        <input type="date" name="from" id="from" value="{{ request('from') }}" class="form-control">
                    </div>
                    <div>
                        <label class="form-label" for="to">To</label>
                        <input type="date" name="to" id="to" value="{{ request('to') }}" class="form-control">
                    </div>
                    <button class="btn btn-secondary" type="submit">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('finance.index') }}" class="btn btn-light">Clear</a>
                </form>
            </div>
        </div>
    </div>

    <div class="finance-ledger-card agri-card">
        <div class="finance-ledger-header">
            <div>
                <h2>Ledger entries</h2>
                <p>
                    @if(request('from') || request('to'))
                        Showing
                        {{ request('from') ? \Illuminate\Support\Carbon::parse(request('from'))->format('M d, Y') : 'start' }}
                        to
                        {{ request('to') ? \Illuminate\Support\Carbon::parse(request('to'))->format('M d, Y') : 'today' }}
                    @else
                        All dates
                    @endif
                    ·
                    {{ ($type ?? 'all') === 'income' ? 'Income only' : (($type ?? 'all') === 'expense' ? 'Expenses only' : 'Income & expenses') }}
                </p>
            </div>
            <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#exportPdfModal">
                <i class="fas fa-download me-1"></i>Export this view
            </button>
        </div>
        <div class="table-responsive">
            <table class="table finance-table align-middle">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Source</th>
                        <th>Amount</th>
                        <th>External ID</th>
                        <th>Bank</th>
                        <th>Sync</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ledger as $row)
                        <tr>
                            <td class="text-nowrap">{{ $row->date?->format('M d, Y') }}</td>
                            <td>
                                @if($row->entry_type === 'income')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="fas fa-arrow-down me-1"></i>Income
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                        <i class="fas fa-arrow-up me-1"></i>Expense
                                    </span>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $row->category }}</td>
                            <td title="{{ $row->description }}">{{ \Illuminate\Support\Str::limit($row->description ?? '—', 55) }}</td>
                            <td class="text-muted small">{{ $row->source }}</td>
                            <td>
                                <span class="finance-amount {{ $row->entry_type === 'income' ? 'text-success' : 'text-danger' }}">
                                    {{ $row->entry_type === 'income' ? '+' : '-' }}₵{{ number_format($row->amount, 2) }}
                                </span>
                            </td>
                            <td class="text-muted small text-nowrap">{{ $row->external_transaction_id ?? '—' }}</td>
                            <td>
                                @if($row->bank_synced)
                                    <span class="finance-sync-chip synced"><i class="fas fa-check-circle"></i>Synced</span>
                                @else
                                    <span class="finance-sync-chip pending"><i class="fas fa-clock"></i>Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($row->can_sync && $row->sync_route && ! $row->bank_synced)
                                    <form action="{{ $row->sync_route }}" method="POST" class="d-inline" data-sync-form>
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Sync with Priority Bank">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </form>
                                @elseif(! $row->bank_synced && ($bankConfigured ?? false))
                                    <span class="text-muted small" title="Use Sync All to Bank">Bulk</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="finance-empty">
                                    <div class="finance-empty-icon"><i class="fas fa-wallet"></i></div>
                                    <h5 class="mb-1">No finance records found</h5>
                                    <p class="text-muted mb-3">Try a different date range, or add income and expenses to get started.</p>
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                                        <a href="{{ route('finance.income.create') }}" class="btn btn-primary btn-sm">Add Income</a>
                                        <a href="{{ route('expenses.create') }}" class="btn btn-outline-primary btn-sm">Add Expense</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($ledger->hasPages())
            <div class="px-4 py-3 border-top">{{ $ledger->withQueryString()->links() }}</div>
        @endif
    </div>
</div>

<div class="modal fade" id="exportPdfModal" tabindex="-1" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="GET" action="{{ route('finance.export-pdf') }}" id="exportPdfForm">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="exportPdfModalLabel">Export Finance PDF</h5>
                        <div class="small opacity-75">Choose what to include and the date range</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Export type</label>
                        <div class="export-type-options">
                            <input type="radio" class="btn-check" name="type" id="exportTypeAll" value="all" {{ ($type ?? 'all') === 'all' ? 'checked' : '' }}>
                            <label class="export-type-option" for="exportTypeAll">
                                <i class="fas fa-layer-group"></i>
                                <strong>All</strong>
                                <span>Income & expenses</span>
                            </label>

                            <input type="radio" class="btn-check" name="type" id="exportTypeIncome" value="income" {{ ($type ?? 'all') === 'income' ? 'checked' : '' }}>
                            <label class="export-type-option" for="exportTypeIncome">
                                <i class="fas fa-arrow-down"></i>
                                <strong>Income</strong>
                                <span>Money in only</span>
                            </label>

                            <input type="radio" class="btn-check" name="type" id="exportTypeExpense" value="expense" {{ ($type ?? 'all') === 'expense' ? 'checked' : '' }}>
                            <label class="export-type-option" for="exportTypeExpense">
                                <i class="fas fa-arrow-up"></i>
                                <strong>Expenses</strong>
                                <span>Money out only</span>
                            </label>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="exportFrom">From date</label>
                            <input type="date" class="form-control" id="exportFrom" name="from" value="{{ request('from') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="exportTo">To date</label>
                            <input type="date" class="form-control" id="exportTo" name="to" value="{{ request('to') }}">
                        </div>
                    </div>
                    <p class="text-muted small mt-3 mb-0">
                        Leave dates blank to export everything for the selected type.
                    </p>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="exportPdfSubmit">
                        <i class="fas fa-file-pdf me-2"></i>Download PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('[data-sync-form]').forEach(function(f) {
    f.addEventListener('submit', function() {
        var btn = this.querySelector('button[type="submit"]');
        if (btn) {
            btn.disabled = true;
            var icon = btn.querySelector('i');
            if (icon) icon.classList.add('fa-spin');
        }
    });
});

var exportForm = document.getElementById('exportPdfForm');
if (exportForm) {
    exportForm.addEventListener('submit', function() {
        var btn = document.getElementById('exportPdfSubmit');
        if (!btn) return;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Preparing PDF...';
        setTimeout(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-file-pdf me-2"></i>Download PDF';
            var modalEl = document.getElementById('exportPdfModal');
            if (modalEl && window.bootstrap) {
                var modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            }
        }, 1500);
    });
}
</script>
@endpush
@endsection
