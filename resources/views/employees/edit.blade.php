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
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ old('is_active', $employee->is_active) ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !old('is_active', $employee->is_active) ? 'selected' : '' }}>Inactive</option>
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

            <div class="row">
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
</script>
@endsection

