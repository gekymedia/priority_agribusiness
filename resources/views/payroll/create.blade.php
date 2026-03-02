@extends('layouts.app')

@section('title', 'New Payroll')

@section('content')
<div class="page-header">
    <h1 class="page-title">New Payroll</h1>
    <p class="page-subtitle">Create a new payroll record for an employee</p>
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

<form method="POST" action="{{ route('payroll.store') }}" id="payrollForm">
    @csrf
    <div class="row g-4">
        <!-- Left: Form -->
        <div class="col-lg-8">
            <div class="agri-card">
                <div class="agri-card-header">
                    <h3><i class="fas fa-file-invoice-dollar me-2"></i>Payroll Details</h3>
                </div>
                <div class="agri-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Employee <span class="text-danger">*</span></label>
                            <select class="form-select" name="employee_id" id="employeeSelect" required>
                                <option value="">-- Select Employee --</option>
                                @foreach($employees as $emp)
                                    @php
                                        $allowances = $emp->allowances ?? [];
                                        $allowancesTotal = $emp->currentAllowancesTotal();
                                    @endphp
                                    <option value="{{ $emp->id }}"
                                            data-name="{{ e($emp->full_name) }}"
                                            data-phone="{{ e($emp->phone) }}"
                                            data-email="{{ e($emp->email) }}"
                                            data-position="{{ e($emp->access_level) }}"
                                            data-base-salary="{{ number_format($emp->base_salary ?? 0, 2, '.', '') }}"
                                            data-allowances='@json($allowances)'
                                            data-allowances-total="{{ number_format($allowancesTotal, 2, '.', '') }}"
                                            data-bank-name="{{ e($emp->bank_name) }}"
                                            data-bank-account-name="{{ e($emp->bank_account_name) }}"
                                            data-bank-account-number="{{ e($emp->bank_account_number) }}"
                                            data-bank-branch="{{ e($emp->bank_branch) }}"
                                            {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Pay Period (Month) <span class="text-danger">*</span></label>
                            <input type="month" class="form-control" id="payMonth" 
                                   value="{{ old('pay_month', now()->format('Y-m')) }}">
                            <input type="hidden" name="pay_period" id="payPeriodHidden" 
                                   value="{{ old('pay_period', now()->startOfMonth()->toDateString()) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Base Salary (GHS) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control" name="base_salary" 
                                   id="baseSalary" value="{{ old('base_salary', 0) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Allowances (GHS)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" class="form-control" name="allowances_total" 
                                       id="allowancesTotal" value="{{ old('allowances_total', 0) }}">
                                <button type="button" class="btn btn-outline-secondary" id="btnUseEmpAllowances" title="Load from employee">
                                    <i class="fas fa-sync"></i>
                                </button>
                            </div>
                            <div class="form-text">Click sync to load employee's default allowances</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Deductions (GHS)</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="deductions_total" 
                                   id="deductionsTotal" value="{{ old('deductions_total', 0) }}">
                        </div>

                        <div class="col-12">
                            <div class="p-3 bg-light border rounded d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="small text-muted">Net Pay (Calculated)</div>
                                    <div class="h4 mb-0 text-success">GHS <span id="netPay">0.00</span></div>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="btnRecalc">
                                    <i class="fas fa-calculator me-2"></i>Recalculate
                                </button>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" name="status">
                                <option value="draft" @selected(old('status') === 'draft')>Draft</option>
                                <option value="approved" @selected(old('status') === 'approved')>Approved</option>
                                <option value="paid" @selected(old('status') === 'paid')>Paid</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="3">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <a href="{{ route('payroll.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Payroll
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Employee Summary -->
        <div class="col-lg-4">
            <div class="agri-card">
                <div class="agri-card-header">
                    <h3><i class="fas fa-user me-2"></i>Employee Info</h3>
                </div>
                <div class="agri-card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-lg mx-auto mb-2" style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 2rem; font-weight: 700;" id="empInitial">?</span>
                        </div>
                        <h5 class="mb-1" id="empName">Select an employee</h5>
                        <p class="text-muted mb-0" id="empPosition">—</p>
                    </div>

                    <div class="border-top pt-3">
                        <div class="mb-2">
                            <i class="fas fa-phone me-2 text-muted"></i>
                            <span id="empPhone">—</span>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-envelope me-2 text-muted"></i>
                            <span id="empEmail">—</span>
                        </div>
                    </div>

                    <div class="border-top pt-3">
                        <h6 class="mb-2">Employee Defaults</h6>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Base Salary:</span>
                            <span>GHS <span id="empBaseSalary">0.00</span></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Allowances:</span>
                            <span>GHS <span id="empAllowancesTotal">0.00</span></span>
                        </div>

                        <details class="mt-2">
                            <summary class="small text-primary" style="cursor: pointer;">View allowances breakdown</summary>
                            <ul class="small mb-0 mt-2" id="empAllowancesList">
                                <li class="text-muted">—</li>
                            </ul>
                        </details>
                    </div>

                    <div class="border-top pt-3 mt-3">
                        <h6 class="mb-2">Bank Details</h6>
                        <div class="small">
                            <div><span class="text-muted">Bank:</span> <span id="empBankName">—</span></div>
                            <div><span class="text-muted">Account:</span> <span id="empBankAccountName">—</span></div>
                            <div><span class="text-muted">Number:</span> <span id="empBankAccountNumber">—</span></div>
                            <div><span class="text-muted">Branch:</span> <span id="empBankBranch">—</span></div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-primary w-100" id="btnLoadDefaults">
                            <i class="fas fa-download me-2"></i>Load Employee Defaults
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sel = document.getElementById('employeeSelect');
    const baseSalary = document.getElementById('baseSalary');
    const allowancesTotal = document.getElementById('allowancesTotal');
    const deductionsTotal = document.getElementById('deductionsTotal');
    const netPay = document.getElementById('netPay');
    const payMonth = document.getElementById('payMonth');
    const payPeriodHidden = document.getElementById('payPeriodHidden');

    const empInitial = document.getElementById('empInitial');
    const empName = document.getElementById('empName');
    const empPosition = document.getElementById('empPosition');
    const empPhone = document.getElementById('empPhone');
    const empEmail = document.getElementById('empEmail');
    const empBaseSalary = document.getElementById('empBaseSalary');
    const empAllowancesTotal = document.getElementById('empAllowancesTotal');
    const empAllowancesList = document.getElementById('empAllowancesList');
    const empBankName = document.getElementById('empBankName');
    const empBankAccountName = document.getElementById('empBankAccountName');
    const empBankAccountNumber = document.getElementById('empBankAccountNumber');
    const empBankBranch = document.getElementById('empBankBranch');

    const btnDefaults = document.getElementById('btnLoadDefaults');
    const btnUseEmpAll = document.getElementById('btnUseEmpAllowances');
    const btnRecalc = document.getElementById('btnRecalc');

    const toNum = v => parseFloat(v || 0) || 0;
    const fmt2 = n => (Math.round(n * 100) / 100).toFixed(2);

    function updateNet() {
        const net = toNum(baseSalary.value) + toNum(allowancesTotal.value) - toNum(deductionsTotal.value);
        netPay.textContent = fmt2(net);
    }

    function fillSummaryFromOption(opt) {
        if (!opt || !opt.value) {
            empInitial.textContent = '?';
            empName.textContent = 'Select an employee';
            empPosition.textContent = '—';
            empPhone.textContent = '—';
            empEmail.textContent = '—';
            empBaseSalary.textContent = '0.00';
            empAllowancesTotal.textContent = '0.00';
            empAllowancesList.innerHTML = '<li class="text-muted">—</li>';
            empBankName.textContent = '—';
            empBankAccountName.textContent = '—';
            empBankAccountNumber.textContent = '—';
            empBankBranch.textContent = '—';
            return;
        }

        const name = opt.dataset.name || '—';
        empInitial.textContent = name.charAt(0).toUpperCase();
        empName.textContent = name;
        empPosition.textContent = opt.dataset.position ? opt.dataset.position.charAt(0).toUpperCase() + opt.dataset.position.slice(1) : '—';
        empPhone.textContent = opt.dataset.phone || '—';
        empEmail.textContent = opt.dataset.email || '—';
        empBaseSalary.textContent = fmt2(toNum(opt.dataset.baseSalary));
        empAllowancesTotal.textContent = fmt2(toNum(opt.dataset.allowancesTotal));

        empAllowancesList.innerHTML = '';
        try {
            const a = opt.dataset.allowances ? JSON.parse(opt.dataset.allowances) : {};
            const keys = Object.keys(a || {});
            if (keys.length === 0) {
                empAllowancesList.innerHTML = '<li class="text-muted">None</li>';
            } else {
                keys.forEach(k => {
                    const li = document.createElement('li');
                    li.textContent = `${k}: GHS ${fmt2(toNum(a[k]))}`;
                    empAllowancesList.appendChild(li);
                });
            }
        } catch (e) {
            empAllowancesList.innerHTML = '<li class="text-muted">—</li>';
        }

        empBankName.textContent = opt.dataset.bankName || '—';
        empBankAccountName.textContent = opt.dataset.bankAccountName || '—';
        empBankAccountNumber.textContent = opt.dataset.bankAccountNumber || '—';
        empBankBranch.textContent = opt.dataset.bankBranch || '—';
    }

    function loadEmployeeDefaultsIntoForm() {
        const opt = sel.options[sel.selectedIndex];
        if (!opt || !opt.value) return;
        baseSalary.value = fmt2(toNum(opt.dataset.baseSalary));
        allowancesTotal.value = fmt2(toNum(opt.dataset.allowancesTotal));
        updateNet();
    }

    sel.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        fillSummaryFromOption(opt);
        loadEmployeeDefaultsIntoForm();
    });

    btnDefaults.addEventListener('click', loadEmployeeDefaultsIntoForm);

    btnUseEmpAll.addEventListener('click', function() {
        const opt = sel.options[sel.selectedIndex];
        if (!opt || !opt.value) return;
        allowancesTotal.value = fmt2(toNum(opt.dataset.allowancesTotal));
        updateNet();
    });

    [baseSalary, allowancesTotal, deductionsTotal].forEach(inp => {
        inp.addEventListener('input', updateNet);
        inp.addEventListener('change', updateNet);
    });

    btnRecalc.addEventListener('click', updateNet);

    const form = document.getElementById('payrollForm');
    form.addEventListener('submit', function() {
        const val = payMonth.value;
        if (val) {
            payPeriodHidden.value = val + '-01';
        }
    });

    updateNet();

    // If there's an old selected employee, fill summary
    @if(old('employee_id'))
    (function() {
        const wanted = '{{ old('employee_id') }}';
        const opt = Array.from(sel.options).find(o => o.value === wanted);
        if (opt) {
            sel.value = wanted;
            fillSummaryFromOption(opt);
        }
    })();
    @endif
});
</script>
@endsection
