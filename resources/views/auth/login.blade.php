@extends('layouts.guest')

@section('title', 'Login')

@section('subtitle', 'Sign in to your account to continue')

@section('auth-content')
<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="form-group-modern">
        <label for="email" class="form-label">
            <i class="fas fa-envelope"></i> Email address
        </label>
        <input type="email" class="form-control-modern @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required autofocus>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group-modern">
        <label for="password" class="form-label">
            <i class="fas fa-lock"></i> Password
        </label>
        <input type="password" class="form-control-modern @error('password') is-invalid @enderror" id="password" name="password" placeholder="Enter your password" required>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group-modern">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label class="form-check-label" for="remember">Remember me</label>
        </div>
    </div>
    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-auth">
            <i class="fas fa-sign-in-alt me-2"></i>Login
        </button>
    </div>
    <div class="text-center">
        <p class="mb-0">Don't have an account? <a href="{{ route('register') }}" class="auth-link">Register here</a></p>
    </div>
</form>
@endsection