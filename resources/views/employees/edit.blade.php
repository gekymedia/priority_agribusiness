@extends('layouts.app')

@section('title', 'Edit Employee')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Employee</h1>
    <p class="page-subtitle">Update employee information and access level</p>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <form method="POST" action="{{ route('employees.update', $employee) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Employee ID</label>
                    <input type="text" value="{{ $employee->employee_id }}" class="form-control" disabled>
                    <small class="text-muted">Employee ID cannot be changed</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Active</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ old('is_active', $employee->is_active) ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !old('is_active', $employee->is_active) ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Approval</label>
                    <select name="status" class="form-select">
                        <option value="pending" {{ old('status', $employee->status) === 'pending' ? 'selected' : '' }}>Pending (cannot log in)</option>
                        <option value="approved" {{ old('status', $employee->status) === 'approved' ? 'selected' : '' }}>Approved (can log in)</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}" class="form-control" required>
                    @error('first_name')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}" class="form-control" required>
                    @error('last_name')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $employee->email) }}" class="form-control" required>
                    @error('email')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" class="form-control">
                    @error('phone')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Access Level <span class="text-danger">*</span></label>
                    <select name="access_level" class="form-select" required>
                        <option value="viewer" {{ old('access_level', $employee->access_level) == 'viewer' ? 'selected' : '' }}>Viewer (Read-only)</option>
                        <option value="caretaker" {{ old('access_level', $employee->access_level) == 'caretaker' ? 'selected' : '' }}>Caretaker (Can edit tasks)</option>
                        <option value="manager" {{ old('access_level', $employee->access_level) == 'manager' ? 'selected' : '' }}>Manager (Full access)</option>
                        <option value="admin" {{ old('access_level', $employee->access_level) == 'admin' ? 'selected' : '' }}>Admin (Full system access)</option>
                    </select>
                    @error('access_level')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Hire Date</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date', $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '') }}" class="form-control">
                    @error('hire_date')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Assign to Farm</label>
                    <select name="farm_id" class="form-select" id="farm_id">
                        <option value="">Select Farm (Optional)</option>
                        @foreach($farms as $farm)
                            <option value="{{ $farm->id }}" {{ old('farm_id', $employee->farm_id) == $farm->id ? 'selected' : '' }}>
                                {{ $farm->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('farm_id')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Assign to House</label>
                    <select name="house_id" class="form-select" id="house_id">
                        <option value="">Select House (Optional)</option>
                        @foreach($houses as $house)
                            <option value="{{ $house->id }}" 
                                data-farm-id="{{ $house->farm_id }}"
                                {{ old('house_id', $employee->house_id) == $house->id ? 'selected' : '' }}>
                                {{ $house->name }} ({{ $house->farm->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('house_id')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address', $employee->address) }}</textarea>
                @error('address')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $employee->notes) }}</textarea>
                @error('notes')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <!-- Salary & Payroll Section -->
            <div class="border-top pt-4 mt-4">
                <h5 class="mb-3"><i class="fas fa-money-bill-wave me-2 text-success"></i>Salary & Payroll Information</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Base Salary (GHS)</label>
                        <input type="number" step="0.01" min="0" name="base_salary" value="{{ old('base_salary', $employee->base_salary ?? 0) }}" class="form-control">
                        @error('base_salary')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Allowances</label>
                    <div id="allowances-container">
                        @php
                            $allowances = old('allowances', $employee->allowances ?? []);
                            if (!is_array($allowances)) $allowances = [];
                        @endphp
                        @forelse($allowances as $name => $amount)
                            <div class="row mb-2 allowance-row">
                                <div class="col-5">
                                    <input type="text" name="allowance_names[]" value="{{ $name }}" class="form-control" placeholder="Allowance name">
                                </div>
                                <div class="col-5">
                                    <input type="number" step="0.01" min="0" name="allowance_amounts[]" value="{{ $amount }}" class="form-control" placeholder="Amount">
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-outline-danger btn-remove-allowance"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        @empty
                            <div class="row mb-2 allowance-row">
                                <div class="col-5">
                                    <input type="text" name="allowance_names[]" class="form-control" placeholder="Allowance name">
                                </div>
                                <div class="col-5">
                                    <input type="number" step="0.01" min="0" name="allowance_amounts[]" class="form-control" placeholder="Amount">
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-outline-danger btn-remove-allowance"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="btn-add-allowance">
                        <i class="fas fa-plus me-1"></i>Add Allowance
                    </button>
                </div>
            </div>

            <!-- Bank Details Section -->
            <div class="border-top pt-4 mt-4">
                <h5 class="mb-3"><i class="fas fa-university me-2 text-primary"></i>Bank Details</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bank Name</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $employee->bank_name) }}" class="form-control">
                        @error('bank_name')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Account Name</label>
                        <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $employee->bank_account_name) }}" class="form-control">
                        @error('bank_account_name')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Account Number</label>
                        <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $employee->bank_account_number) }}" class="form-control">
                        @error('bank_account_number')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Branch</label>
                        <input type="text" name="bank_branch" value="{{ old('bank_branch', $employee->bank_branch) }}" class="form-control">
                        @error('bank_branch')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control">
                    <small class="text-muted">Leave blank to keep current password</small>
                    @error('password')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-agri">
                    <i class="fas fa-save me-2"></i>Update Employee
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Filter houses based on selected farm
    document.getElementById('farm_id').addEventListener('change', function() {
        const farmId = this.value;
        const houseSelect = document.getElementById('house_id');
        const options = houseSelect.querySelectorAll('option');
        
        options.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
            } else {
                const optionFarmId = option.getAttribute('data-farm-id');
                if (farmId === '' || optionFarmId === farmId) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            }
        });
    });

    // Add allowance row
    document.getElementById('btn-add-allowance').addEventListener('click', function() {
        const container = document.getElementById('allowances-container');
        const row = document.createElement('div');
        row.className = 'row mb-2 allowance-row';
        row.innerHTML = `
            <div class="col-5">
                <input type="text" name="allowance_names[]" class="form-control" placeholder="Allowance name">
            </div>
            <div class="col-5">
                <input type="number" step="0.01" min="0" name="allowance_amounts[]" class="form-control" placeholder="Amount">
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-outline-danger btn-remove-allowance"><i class="fas fa-times"></i></button>
            </div>
        `;
        container.appendChild(row);
    });

    // Remove allowance row
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-allowance')) {
            const row = e.target.closest('.allowance-row');
            if (document.querySelectorAll('.allowance-row').length > 1) {
                row.remove();
            } else {
                row.querySelectorAll('input').forEach(input => input.value = '');
            }
        }
    });
</script>
@endsection

