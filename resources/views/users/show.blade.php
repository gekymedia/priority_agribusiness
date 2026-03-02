@extends('layouts.app')

@section('title', 'View User')

@section('content')
<div class="page-header">
    <h1 class="page-title">User Details</h1>
    <p class="page-subtitle">View user account information</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="agri-card">
            <div class="agri-card-header">
                <h3><i class="fas fa-user me-2"></i>{{ $user->name }}</h3>
            </div>
            <div class="agri-card-body">
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">User ID</div>
                    <div class="col-md-8"><strong>USR-{{ $user->id }}</strong></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Name</div>
                    <div class="col-md-8">{{ $user->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Email</div>
                    <div class="col-md-8">{{ $user->email }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Phone</div>
                    <div class="col-md-8">{{ $user->phone ?? 'N/A' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Role</div>
                    <div class="col-md-8">
                        <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : 'info' }}">
                            {{ ucfirst($user->role ?? 'user') }}
                        </span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Registered</div>
                    <div class="col-md-8">{{ $user->created_at->format('M d, Y h:i A') }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Last Updated</div>
                    <div class="col-md-8">{{ $user->updated_at->format('M d, Y h:i A') }}</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="agri-card">
            <div class="agri-card-header">
                <h3><i class="fas fa-cogs me-2"></i>Actions</h3>
            </div>
            <div class="agri-card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit User
                    </a>
                    
                    @php
                        $currentUser = auth()->user();
                        $canImpersonate = ($currentUser instanceof \App\Models\User) || 
                                         ($currentUser instanceof \App\Models\Employee && $currentUser->isAdmin());
                        $isNotSelf = !($currentUser instanceof \App\Models\User && $currentUser->id === $user->id);
                    @endphp
                    
                    @if($canImpersonate && $isNotSelf)
                        <form action="{{ route('impersonate.user', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-dark w-100">
                                <i class="fas fa-user-secret me-2"></i>Impersonate User
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('employees.index', ['type_filter' => 'users']) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                    
                    @if($isNotSelf)
                    <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash me-2"></i>Delete User
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
