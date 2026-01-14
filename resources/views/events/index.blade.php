@extends('layouts.guest')

@section('title', 'Events')

@section('subtitle', 'Stay updated with the latest events and workshops')

@section('auth-content')
<div class="events-list">
    <div class="event-item mb-4 p-3" style="background: rgba(46, 125, 50, 0.05); border-radius: 12px; border-left: 4px solid var(--primary);">
        <div class="d-flex align-items-start">
            <div class="me-3">
                <i class="fas fa-calendar-alt text-primary" style="font-size: 24px;"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="mb-2" style="color: var(--text); font-weight: 700;">Farm Management Workshop</h5>
                <p class="mb-2" style="color: var(--text-light);">Learn best practices for managing your poultry and crop farms.</p>
                <p class="mb-0"><small style="color: var(--text-light);"><i class="fas fa-clock me-1"></i>December 25, 2025 | 10:00 AM</small></p>
            </div>
        </div>
    </div>
    
    <div class="event-item mb-4 p-3" style="background: rgba(46, 125, 50, 0.05); border-radius: 12px; border-left: 4px solid var(--accent);">
        <div class="d-flex align-items-start">
            <div class="me-3">
                <i class="fas fa-seedling text-success" style="font-size: 24px;"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="mb-2" style="color: var(--text); font-weight: 700;">Modern Farming Techniques</h5>
                <p class="mb-2" style="color: var(--text-light);">Discover innovative techniques for improving your farm productivity.</p>
                <p class="mb-0"><small style="color: var(--text-light);"><i class="fas fa-clock me-1"></i>January 15, 2026 | 2:00 PM</small></p>
            </div>
        </div>
    </div>
    
    <div class="event-item mb-4 p-3" style="background: rgba(46, 125, 50, 0.05); border-radius: 12px; border-left: 4px solid var(--secondary);">
        <div class="d-flex align-items-start">
            <div class="me-3">
                <i class="fas fa-heartbeat text-warning" style="font-size: 24px;"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="mb-2" style="color: var(--text); font-weight: 700;">Livestock Health Management</h5>
                <p class="mb-2" style="color: var(--text-light);">Expert session on maintaining healthy livestock and preventing diseases.</p>
                <p class="mb-0"><small style="color: var(--text-light);"><i class="fas fa-clock me-1"></i>February 10, 2026 | 9:00 AM</small></p>
            </div>
        </div>
    </div>
</div>

<div class="divider">
    <span>Join Us</span>
</div>

<div class="text-center">
    <p class="mb-3" style="color: var(--text-light);">Want to participate in our events?</p>
    <div class="d-grid gap-2">
        <a href="{{ route('register') }}" class="btn btn-auth">
            <i class="fas fa-user-plus me-2"></i>Create an Account
        </a>
        <a href="{{ route('login') }}" class="btn btn-auth-outline">
            <i class="fas fa-sign-in-alt me-2"></i>Login
        </a>
    </div>
</div>
@endsection

@section('show-features')
true
@endsection
