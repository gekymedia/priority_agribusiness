@extends('layouts.app')

@section('title', 'My Payslips')

@section('content')
<div class="page-header">
    <h1 class="page-title">My Payslips</h1>
    <p class="page-subtitle">View your salary payment history</p>
</div>

@php
    $sort = $sort ?? 'pay_period_end';
    $direction = $direction ?? 'desc';
@endphp
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="agri-card">
    <div class="agri-card-header">
        <h3><i class="fas fa-file-invoice-dollar me-2"></i>Payment History</h3>
    </div>
    <div class="agri-card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        @php
                            $sortUrl = fn ($col) => request()->fullUrlWithQuery(['sort' => $col, 'direction' => ($sort === $col && $direction === 'asc') ? 'desc' : 'asc', 'page' => null]);
                            $sortIcon = fn ($col) => $sort === $col ? ($direction === 'asc' ? ' fa-sort-up' : ' fa-sort-down') : ' fa-sort text-muted';
                        @endphp
                        <th><a href="{{ $sortUrl('pay_period_end') }}" class="text-decoration-none text-dark">Pay Period</a><i class="fas{{ $sortIcon('pay_period_end') }} ms-1"></i></th>
                        <th><a href="{{ $sortUrl('base_salary') }}" class="text-decoration-none text-dark">Base Salary</a><i class="fas{{ $sortIcon('base_salary') }} ms-1"></i></th>
                        <th><a href="{{ $sortUrl('allowances_total') }}" class="text-decoration-none text-dark">Allowances</a><i class="fas{{ $sortIcon('allowances_total') }} ms-1"></i></th>
                        <th><a href="{{ $sortUrl('deductions_total') }}" class="text-decoration-none text-dark">Deductions</a><i class="fas{{ $sortIcon('deductions_total') }} ms-1"></i></th>
                        <th><a href="{{ $sortUrl('net_pay') }}" class="text-decoration-none text-dark">Net Pay</a><i class="fas{{ $sortIcon('net_pay') }} ms-1"></i></th>
                        <th><a href="{{ $sortUrl('status') }}" class="text-decoration-none text-dark">Status</a><i class="fas{{ $sortIcon('status') }} ms-1"></i></th>
                        <th><a href="{{ $sortUrl('paid_at') }}" class="text-decoration-none text-dark">Paid Date</a><i class="fas{{ $sortIcon('paid_at') }} ms-1"></i></th>
                        <th>Actions</th>
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
                        <tr>
                            <td class="fw-bold">
                                {{ \Carbon\Carbon::parse($payroll->pay_period_start)->format('M d') }} - 
                                {{ \Carbon\Carbon::parse($payroll->pay_period_end)->format('M d, Y') }}
                            </td>
                            <td>GHS {{ number_format($payroll->base_salary, 2) }}</td>
                            <td>GHS {{ number_format($payroll->allowances_total, 2) }}</td>
                            <td>GHS {{ number_format($payroll->deductions_total, 2) }}</td>
                            <td class="fw-bold text-success">GHS {{ number_format($payroll->net_pay, 2) }}</td>
                            <td>
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($payroll->status) }}</span>
                            </td>
                            <td>
                                @if($payroll->paid_at)
                                    {{ $payroll->paid_at->format('M d, Y') }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('payslips.show', $payroll) }}" class="btn btn-sm btn-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                No payslips found
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
@endsection
