@extends('layouts.app')

@section('title', 'Payroll')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Payroll</h1>
        <p class="page-subtitle">Manage employee salary payments</p>
    </div>
    <a href="{{ route('payroll.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>New Payroll
    </a>
</div>

<!-- Filter -->
<div class="agri-card mb-4">
    <div class="agri-card-body">
        <form class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Filter by Month</label>
                <input type="month" class="form-control" name="month" value="{{ $month }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter me-2"></i>Filter
                </button>
            </div>
            @if($month)
            <div class="col-md-2">
                <a href="{{ route('payroll.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Clear
                </a>
            </div>
            @endif
        </form>
    </div>
</div>

@php
    $sort = $sort ?? 'pay_period';
    $direction = $direction ?? 'desc';
@endphp
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Payroll Table -->
<div class="agri-card">
    <div class="agri-card-header">
        <h3><i class="fas fa-money-check-alt me-2"></i>Payroll Records</h3>
    </div>
    <div class="agri-card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        @php
                            $sortUrl = fn ($col) => request()->fullUrlWithQuery(array_merge(request()->only(['month']), ['sort' => $col, 'direction' => ($sort === $col && $direction === 'asc') ? 'desc' : 'asc', 'page' => null]));
                            $sortIcon = fn ($col) => $sort === $col ? ($direction === 'asc' ? ' fa-sort-up' : ' fa-sort-down') : ' fa-sort text-muted';
                        @endphp
                        <th><a href="{{ $sortUrl('employee') }}" class="text-decoration-none text-dark">Employee</a><i class="fas{{ $sortIcon('employee') }} ms-1"></i></th>
                        <th><a href="{{ $sortUrl('pay_period') }}" class="text-decoration-none text-dark">Period</a><i class="fas{{ $sortIcon('pay_period') }} ms-1"></i></th>
                        <th><a href="{{ $sortUrl('base_salary') }}" class="text-decoration-none text-dark">Base Salary</a><i class="fas{{ $sortIcon('base_salary') }} ms-1"></i></th>
                        <th><a href="{{ $sortUrl('allowances_total') }}" class="text-decoration-none text-dark">Allowances</a><i class="fas{{ $sortIcon('allowances_total') }} ms-1"></i></th>
                        <th><a href="{{ $sortUrl('deductions_total') }}" class="text-decoration-none text-dark">Deductions</a><i class="fas{{ $sortIcon('deductions_total') }} ms-1"></i></th>
                        <th><a href="{{ $sortUrl('net_pay') }}" class="text-decoration-none text-dark">Net Pay</a><i class="fas{{ $sortIcon('net_pay') }} ms-1"></i></th>
                        <th><a href="{{ $sortUrl('status') }}" class="text-decoration-none text-dark">Status</a><i class="fas{{ $sortIcon('status') }} ms-1"></i></th>
                        <th><a href="{{ $sortUrl('paid_at') }}" class="text-decoration-none text-dark">Paid At</a><i class="fas{{ $sortIcon('paid_at') }} ms-1"></i></th>
                        <th width="140">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $payroll)
                        @php
                            $badgeClass = match($payroll->status) {
                                'draft' => 'bg-secondary',
                                'approved' => 'bg-warning',
                                'paid' => 'bg-success',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <tr data-row-id="{{ $payroll->id }}">
                            <td class="fw-bold">{{ $payroll->employee->full_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($payroll->pay_period)->format('M Y') }}</td>
                            <td>GHS {{ number_format($payroll->base_salary, 2) }}</td>
                            <td>GHS {{ number_format($payroll->allowances_total, 2) }}</td>
                            <td>GHS {{ number_format($payroll->deductions_total, 2) }}</td>
                            <td class="fw-bold text-success">GHS {{ number_format($payroll->net_pay, 2) }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <select class="form-select form-select-sm payroll-status" 
                                            data-id="{{ $payroll->id }}"
                                            data-current="{{ $payroll->status }}"
                                            style="width: auto;">
                                        <option value="draft" @selected($payroll->status === 'draft')>Draft</option>
                                        <option value="approved" @selected($payroll->status === 'approved')>Approved</option>
                                        <option value="paid" @selected($payroll->status === 'paid')>Paid</option>
                                    </select>
                                    <span class="badge {{ $badgeClass }} status-badge">{{ ucfirst($payroll->status) }}</span>
                                    <div class="spinner-border spinner-border-sm text-secondary d-none status-spinner" role="status"></div>
                                </div>
                            </td>
                            <td class="paid-at-cell">
                                {{ $payroll->paid_at ? $payroll->paid_at->format('Y-m-d H:i') : '—' }}
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('payroll.edit', $payroll) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('payroll.destroy', $payroll) }}" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this payroll record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                No payroll records found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payrolls->hasPages())
            <div class="mt-3">
                {{ $payrolls->links() }}
            </div>
        @endif
    </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
document.addEventListener('DOMContentLoaded', function() {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function badgeClass(status) {
        return {
            'draft': 'bg-secondary',
            'approved': 'bg-warning',
            'paid': 'bg-success'
        }[status] || 'bg-secondary';
    }

    document.querySelectorAll('.payroll-status').forEach(function(sel) {
        sel.addEventListener('change', async function() {
            const id = this.dataset.id;
            const newStatus = this.value;
            const row = this.closest('tr');
            const spinner = row.querySelector('.status-spinner');
            const badge = row.querySelector('.status-badge');
            const paidAtCell = row.querySelector('.paid-at-cell');
            const oldStatus = this.dataset.current;

            this.disabled = true;
            spinner.classList.remove('d-none');

            try {
                const res = await fetch("{{ url('/payroll') }}/" + id + "/status", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: newStatus })
                });

                if (!res.ok) throw new Error('Request failed');
                const json = await res.json();

                badge.className = 'badge status-badge ' + badgeClass(json.status);
                badge.textContent = json.status_label;
                paidAtCell.textContent = json.paid_at ?? '—';
                this.dataset.current = json.status;
            } catch (e) {
                alert('Could not update status. Please try again.');
                this.value = oldStatus;
            } finally {
                spinner.classList.add('d-none');
                this.disabled = false;
            }
        });
    });
});
</script>
@endsection
