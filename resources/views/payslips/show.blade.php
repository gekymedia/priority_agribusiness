@extends('layouts.app')

@section('title', 'Payslip Details')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Payslip Details</h1>
        <p class="page-subtitle">
            Pay Period: {{ \Carbon\Carbon::parse($payroll->pay_period_start)->format('M d') }} - 
            {{ \Carbon\Carbon::parse($payroll->pay_period_end)->format('M d, Y') }}
        </p>
    </div>
    <a href="{{ route('payslips.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Payslips
    </a>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-8">
        <div class="agri-card mb-4">
            <div class="agri-card-header d-flex justify-content-between align-items-center">
                <h3><i class="fas fa-file-invoice-dollar me-2"></i>Payment Summary</h3>
                @php
                    $badgeClass = match($payroll->status) {
                        'draft' => 'bg-secondary',
                        'approved' => 'bg-warning',
                        'paid' => 'bg-success',
                        default => 'bg-secondary'
                    };
                @endphp
                <span class="badge {{ $badgeClass }} fs-6">{{ ucfirst($payroll->status) }}</span>
            </div>
            <div class="agri-card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Earnings</h6>
                        <table class="table table-sm">
                            <tr>
                                <td>Base Salary</td>
                                <td class="text-end">GHS {{ number_format($payroll->base_salary, 2) }}</td>
                            </tr>
                            @if($payroll->allowances && is_array($payroll->allowances))
                                @foreach($payroll->allowances as $name => $amount)
                                    <tr>
                                        <td>{{ $name }}</td>
                                        <td class="text-end">GHS {{ number_format($amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            <tr class="table-light fw-bold">
                                <td>Total Earnings</td>
                                <td class="text-end">GHS {{ number_format($payroll->base_salary + $payroll->allowances_total, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Deductions</h6>
                        <table class="table table-sm">
                            @if($payroll->deductions && is_array($payroll->deductions))
                                @foreach($payroll->deductions as $name => $amount)
                                    <tr>
                                        <td>{{ $name }}</td>
                                        <td class="text-end text-danger">- GHS {{ number_format($amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2" class="text-muted">No deductions</td>
                                </tr>
                            @endif
                            <tr class="table-light fw-bold">
                                <td>Total Deductions</td>
                                <td class="text-end text-danger">- GHS {{ number_format($payroll->deductions_total, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center p-3 bg-success bg-opacity-10 rounded">
                            <h4 class="mb-0">Net Pay</h4>
                            <h3 class="mb-0 text-success">GHS {{ number_format($payroll->net_pay, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="agri-card mb-4">
            <div class="agri-card-header">
                <h3><i class="fas fa-info-circle me-2"></i>Payment Info</h3>
            </div>
            <div class="agri-card-body">
                <table class="table table-sm">
                    <tr>
                        <td class="text-muted">Pay Period Start</td>
                        <td class="text-end">{{ \Carbon\Carbon::parse($payroll->pay_period_start)->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Pay Period End</td>
                        <td class="text-end">{{ \Carbon\Carbon::parse($payroll->pay_period_end)->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td class="text-end">
                            <span class="badge {{ $badgeClass }}">{{ ucfirst($payroll->status) }}</span>
                        </td>
                    </tr>
                    @if($payroll->paid_at)
                        <tr>
                            <td class="text-muted">Paid On</td>
                            <td class="text-end">{{ $payroll->paid_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-muted">Created</td>
                        <td class="text-end">{{ $payroll->created_at->format('M d, Y') }}</td>
                    </tr>
                </table>

                @if($payroll->notes)
                    <hr>
                    <h6 class="text-muted">Notes</h6>
                    <p class="mb-0">{{ $payroll->notes }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
