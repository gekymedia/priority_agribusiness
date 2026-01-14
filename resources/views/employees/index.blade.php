@extends('layouts.app')

@section('title', 'Employees')

@section('content')
<div class="page-header">
    <h1 class="page-title">Employees</h1>
    <p class="page-subtitle">Manage farm employees and their access levels</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('employees.create') }}" class="btn btn-agri">
            <i class="fas fa-plus me-2"></i>Add Employee
        </a>
    </div>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Access Level</th>
                        <th>Farm/House</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                    <tr>
                        <td><strong>{{ $employee->employee_id }}</strong></td>
                        <td>{{ $employee->full_name }}</td>
                        <td>{{ $employee->email }}</td>
                        <td>{{ $employee->phone ?? 'N/A' }}</td>
                        <td>
                            @php
                                $badgeColors = [
                                    'admin' => 'danger',
                                    'manager' => 'warning',
                                    'caretaker' => 'primary',
                                    'viewer' => 'secondary'
                                ];
                            @endphp
                            <span class="badge bg-{{ $badgeColors[$employee->access_level] ?? 'secondary' }}">
                                {{ ucfirst($employee->access_level) }}
                            </span>
                        </td>
                        <td>
                            @if($employee->farm)
                                <span class="badge bg-info">{{ $employee->farm->name }}</span>
                            @elseif($employee->house)
                                <span class="badge bg-info">{{ $employee->house->name }}</span>
                            @else
                                <span class="text-muted">Not assigned</span>
                            @endif
                        </td>
                        <td>
                            @if($employee->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No employees registered yet</p>
                            <a href="{{ route('employees.create') }}" class="btn btn-agri">
                                <i class="fas fa-plus me-2"></i>Add First Employee
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
        <div class="mt-4">
            {{ $employees->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

