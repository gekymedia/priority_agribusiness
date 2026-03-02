@extends('layouts.app')

@section('title', 'Edit Payroll')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Payroll</h1>
    <p class="page-subtitle">Update payroll record for {{ $payroll->employee->full_name }}</p>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="agri-card">
    <div class="agri-card-header">
        <h3><i class="fas fa-edit me-2"></i>Payroll Details</h3>
    </div>
    <div class="agri-card-body">
        <form method="POST" action="{{ route('payroll.update', $payroll) }}">
            @csrf
            @method('PUT')
            
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Employee <span class="text-danger">*</span></label>
                    <select class="form-select" name="employee_id" required>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" @selected($emp->id === $payroll->employee_id)>
                                {{ $emp->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Pay Period <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="pay_period" 
                           value="{{ $payroll->pay_period->toDateString() }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select" name="status">
                        <option value="draft" @selected($payroll->status === 'draft')>Draft</option>
                        <option value="approved" @selected($payroll->status === 'approved')>Approved</option>
                        <option value="paid" @selected($payroll->status === 'paid')>Paid</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Base Salary (GHS) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" class="form-control" name="base_salary" 
                           id="baseSalary" value="{{ $payroll->base_salary }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Allowances (GHS)</label>
                    <input type="number" step="0.01" min="0" class="form-control" name="allowances_total" 
                           id="allowancesTotal" value="{{ $payroll->allowances_total }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Deductions (GHS)</label>
                    <input type="number" step="0.01" min="0" class="form-control" name="deductions_total" 
                           id="deductionsTotal" value="{{ $payroll->deductions_total }}">
                </div>

                <div class="col-12">
                    <div class="p-3 bg-light border rounded d-flex align-items-center justify-content-between">
                        <div>
                            <div class="small text-muted">Net Pay (Calculated)</div>
                            <div class="h4 mb-0 text-success">GHS <span id="netPay">{{ number_format($payroll->net_pay, 2) }}</span></div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="btnRecalc">
                            <i class="fas fa-calculator me-2"></i>Recalculate
                        </button>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" name="notes" rows="3">{{ $payroll->notes }}</textarea>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('payroll.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Payroll
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseSalary = document.getElementById('baseSalary');
    const allowancesTotal = document.getElementById('allowancesTotal');
    const deductionsTotal = document.getElementById('deductionsTotal');
    const netPay = document.getElementById('netPay');
    const btnRecalc = document.getElementById('btnRecalc');

    const toNum = v => parseFloat(v || 0) || 0;
    const fmt2 = n => (Math.round(n * 100) / 100).toFixed(2);

    function updateNet() {
        const net = toNum(baseSalary.value) + toNum(allowancesTotal.value) - toNum(deductionsTotal.value);
        netPay.textContent = fmt2(net);
    }

    [baseSalary, allowancesTotal, deductionsTotal].forEach(inp => {
        inp.addEventListener('input', updateNet);
        inp.addEventListener('change', updateNet);
    });

    btnRecalc.addEventListener('click', updateNet);
});
</script>
@endsection
