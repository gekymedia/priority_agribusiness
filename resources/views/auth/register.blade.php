@extends('layouts.guest')

@section('title', 'Register')

@section('subtitle', 'Join us to start managing your agribusiness')

@section('auth-content')
<form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="form-group-modern">
        <label for="name" class="form-label">
            <i class="fas fa-user"></i> Full Name
        </label>
        <input type="text" class="form-control-modern @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Enter your full name" required autofocus>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group-modern">
        <label for="email" class="form-label">
            <i class="fas fa-envelope"></i> Email address
        </label>
        <input type="email" class="form-control-modern @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group-modern">
        <label for="password" class="form-label">
            <i class="fas fa-lock"></i> Password
        </label>
        <input type="password" class="form-control-modern @error('password') is-invalid @enderror" id="password" name="password" placeholder="Create a password" required>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted mt-2 d-block">Must be at least 8 characters</small>
    </div>
    <div class="form-group-modern">
        <label for="password_confirmation" class="form-label">
            <i class="fas fa-lock"></i> Confirm Password
        </label>
        <input type="password" class="form-control-modern" id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" required>
    </div>
    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-auth">
            <i class="fas fa-user-plus me-2"></i>Create Account
        </button>
    </div>
    <div class="text-center">
        <p class="mb-0">Already have an account? <a href="{{ route('login') }}" class="auth-link">Login here</a></p>
    </div>
</form>
@endsection