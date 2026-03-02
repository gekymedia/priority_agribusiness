@extends('layouts.app')

@section('title', 'Employees & Users')

@section('content')
<div class="page-header">
    <h1 class="page-title">Employees & Users</h1>
    <p class="page-subtitle">Manage system access for employees and users</p>
</div>

@if($pendingCount > 0)
<div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
    <i class="fas fa-user-clock fa-2x me-3"></i>
    <div class="flex-grow-1">
        <strong>{{ $pendingCount }}</strong> employee(s) pending approval. They cannot log in until you approve them.
    </div>
    <a href="{{ route('employees.index', ['status_filter' => 'pending', 'type_filter' => 'employees']) }}" class="btn btn-warning btn-sm">View pending</a>
</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('employees.create') }}" class="btn btn-agri">
            <i class="fas fa-plus me-2"></i>Add Employee
        </a>
        
        <div class="btn-group">
            <a href="{{ route('employees.index', ['type_filter' => 'all']) }}" 
               class="btn btn-outline-primary {{ $typeFilter === 'all' ? 'active' : '' }}">
                All ({{ $employeeCount + $userCount }})
            </a>
            <a href="{{ route('employees.index', ['type_filter' => 'employees']) }}" 
               class="btn btn-outline-primary {{ $typeFilter === 'employees' ? 'active' : '' }}">
                <i class="fas fa-id-badge me-1"></i>Employees ({{ $employeeCount }})
            </a>
            <a href="{{ route('employees.index', ['type_filter' => 'users']) }}" 
               class="btn btn-outline-primary {{ $typeFilter === 'users' ? 'active' : '' }}">
                <i class="fas fa-user me-1"></i>Users ({{ $userCount }})
            </a>
        </div>

        @if($typeFilter === 'employees' || $typeFilter === 'all')
        <div class="btn-group">
            <a href="{{ route('employees.index', ['type_filter' => $typeFilter, 'status_filter' => 'pending']) }}" 
               class="btn btn-outline-warning {{ $statusFilter === 'pending' ? 'active' : '' }}">
                <i class="fas fa-user-clock me-1"></i>Pending
            </a>
            <a href="{{ route('employees.index', ['type_filter' => $typeFilter, 'status_filter' => 'approved']) }}" 
               class="btn btn-outline-success {{ $statusFilter === 'approved' ? 'active' : '' }}">
                <i class="fas fa-user-check me-1"></i>Approved
            </a>
        </div>
        @endif

        @if($statusFilter)
        <a href="{{ route('employees.index', ['type_filter' => $typeFilter]) }}" class="btn btn-outline-secondary">
            <i class="fas fa-times me-1"></i>Clear Filter
        </a>
        @endif
    </div>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                    <tr>
                        <td>
                            @if($record->record_type === 'employee')
                                <span class="badge bg-primary"><i class="fas fa-id-badge me-1"></i>Employee</span>
                            @else
                                <span class="badge bg-info"><i class="fas fa-user me-1"></i>User</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $record->record_type === 'employee' ? $record->employee_id : 'USR-' . $record->id }}</strong>
                        </td>
                        <td>{{ $record->display_name }}</td>
                        <td>{{ $record->email }}</td>
                        <td>{{ $record->phone ?? 'N/A' }}</td>
                        <td>
                            @php
                                $roleBadgeColors = [
                                    'Admin' => 'danger',
                                    'Poultry Farm Manager' => 'warning',
                                    'Crop Farms Manager' => 'success',
                                ];
                            @endphp
                            <span class="badge bg-{{ $roleBadgeColors[$record->display_role] ?? 'secondary' }}">
                                {{ $record->display_role }}
                            </span>
                        </td>
                        <td>
                            @if($record->status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>
                        <td>
                            @if($record->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $currentUser = auth()->user();
                                $canImpersonate = ($currentUser instanceof \App\Models\User) || 
                                                 ($currentUser instanceof \App\Models\Employee && $currentUser->isAdmin());
                            @endphp
                            
                            @if($record->record_type === 'employee')
                                @if($record->status === 'pending')
                                    <form action="{{ route('employees.approve', $record) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success" title="Approve employee">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                @php
                                    $isNotSelf = !($currentUser instanceof \App\Models\Employee && $currentUser->id === $record->id);
                                @endphp
                                
                                @if($canImpersonate && $isNotSelf && $record->status === 'approved')
                                    <form action="{{ route('impersonate.start', $record) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-dark" title="Impersonate this employee">
                                            <i class="fas fa-user-secret"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                <a href="{{ route('employees.show', $record) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('employees.edit', $record) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('employees.destroy', $record) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @else
                                @php
                                    $isNotSelf = !($currentUser instanceof \App\Models\User && $currentUser->id === $record->id);
                                @endphp
                                
                                @if($canImpersonate && $isNotSelf)
                                    <form action="{{ route('impersonate.user', $record) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-dark" title="Impersonate this user">
                                            <i class="fas fa-user-secret"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                <a href="{{ route('users.show', $record) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('users.edit', $record) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('users.destroy', $record) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No employees or users found</p>
                            <a href="{{ route('employees.create') }}" class="btn btn-agri">
                                <i class="fas fa-plus me-2"></i>Add First Employee
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
        <div class="mt-4">
            {{ $records->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
