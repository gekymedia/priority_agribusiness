@extends('layouts.app')

@section('title', 'Employees')

@section('content')
<div class="page-header">
    <h1 class="page-title">Employees</h1>
    <p class="page-subtitle">Manage farm employees and their access levels</p>
</div>

@if($pendingCount > 0)
<div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
    <i class="fas fa-user-clock fa-2x me-3"></i>
    <div class="flex-grow-1">
        <strong>{{ $pendingCount }}</strong> employee(s) pending approval. They cannot log in until you approve them.
    </div>
    <a href="{{ route('employees.index', ['status_filter' => 'pending']) }}" class="btn btn-warning btn-sm">View pending</a>
</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div class="d-flex gap-2">
        <a href="{{ route('employees.create') }}" class="btn btn-agri">
            <i class="fas fa-plus me-2"></i>Add Employee
        </a>
        <a href="{{ route('employees.index', ['status_filter' => 'pending']) }}" class="btn btn-outline-warning {{ request('status_filter') === 'pending' ? 'active' : '' }}">
            <i class="fas fa-user-clock me-2"></i>Pending
        </a>
        <a href="{{ route('employees.index', ['status_filter' => 'approved']) }}" class="btn btn-outline-success {{ request('status_filter') === 'approved' ? 'active' : '' }}">
            <i class="fas fa-user-check me-2"></i>Approved
        </a>
        @if(request('status_filter'))
        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">All</a>
        @endif
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
                        <th>Approval</th>
                        <th>Active</th>
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
                            @if($employee->status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
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
                            @if($employee->status === 'pending')
                                <form action="{{ route('employees.approve', $employee) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success" title="Approve employee">
                                        <i class="fas fa-check me-1"></i>Approve
                                    </button>
                                </form>
                            @endif
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
                        <td colspan="9" class="text-center py-5">
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

