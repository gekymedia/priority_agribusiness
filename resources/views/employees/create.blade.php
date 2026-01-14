@extends('layouts.app')

@section('title', 'Add Employee')

@section('content')
<div class="page-header">
    <h1 class="page-title">Add Employee</h1>
    <p class="page-subtitle">Register a new employee with access level</p>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <form method="POST" action="{{ route('employees.store') }}">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" class="form-control" required>
                    @error('first_name')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-control" required>
                    @error('last_name')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                    @error('email')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
                    @error('phone')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Access Level <span class="text-danger">*</span></label>
                    <select name="access_level" class="form-select" required>
                        <option value="viewer" {{ old('access_level') == 'viewer' ? 'selected' : '' }}>Viewer (Read-only)</option>
                        <option value="caretaker" {{ old('access_level') == 'caretaker' ? 'selected' : '' }}>Caretaker (Can edit tasks)</option>
                        <option value="manager" {{ old('access_level') == 'manager' ? 'selected' : '' }}>Manager (Full access)</option>
                        <option value="admin" {{ old('access_level') == 'admin' ? 'selected' : '' }}>Admin (Full system access)</option>
                    </select>
                    @error('access_level')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Select the appropriate access level for this employee</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Hire Date</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date') }}" class="form-control">
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
                            <option value="{{ $farm->id }}" {{ old('farm_id') == $farm->id ? 'selected' : '' }}>
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
                                {{ old('house_id') == $house->id ? 'selected' : '' }}>
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
                <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                @error('address')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                @error('notes')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" required>
                    @error('password')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-agri">
                    <i class="fas fa-save me-2"></i>Create Employee
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
        
        // Reset house selection if farm changed
        if (farmId !== '') {
            houseSelect.value = '';
        }
    });
</script>
@endsection

