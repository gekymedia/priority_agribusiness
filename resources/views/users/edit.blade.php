@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit User</h1>
    <p class="page-subtitle">Update user account information</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="agri-card">
            <div class="agri-card-header">
                <h3><i class="fas fa-user-edit me-2"></i>User Information</h3>
            </div>
            <div class="agri-card-body">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control @error('phone') is-invalid @enderror">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                                <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    <h5 class="mb-3"><i class="fas fa-lock me-2 text-warning"></i>Change Password (optional)</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Leave blank to keep current">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password">
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-agri">
                            <i class="fas fa-save me-2"></i>Update User
                        </button>
                        <a href="{{ route('employees.index', ['type_filter' => 'users']) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="agri-card">
            <div class="agri-card-header">
                <h3><i class="fas fa-info-circle me-2"></i>User Info</h3>
            </div>
            <div class="agri-card-body">
                <p><strong>User ID:</strong> USR-{{ $user->id }}</p>
                <p><strong>Registered:</strong> {{ $user->created_at->format('M d, Y') }}</p>
                <p><strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
