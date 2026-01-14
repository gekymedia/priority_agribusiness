@extends('layouts.app')

@section('title', 'Medication Calendars')

@section('content')
<div class="page-header">
    <h1 class="page-title">Medication Calendars</h1>
    <p class="page-subtitle">View available medication and vaccination schedule templates</p>
</div>

<div class="row g-4">
    @forelse($calendars as $calendar)
        <div class="col-lg-6">
            <div class="agri-card h-100">
                <div class="agri-card-header" style="background: linear-gradient(135deg, rgba(46, 125, 50, 0.9), rgba(139, 195, 74, 0.9));">
                    <h3>
                        <i class="fas fa-calendar-alt me-2"></i>{{ $calendar->name }}
                        @if($calendar->is_default)
                            <span class="badge bg-light text-dark ms-2">Default</span>
                        @endif
                    </h3>
                </div>
                <div class="agri-card-body">
                    <div class="mb-3">
                        <span class="badge bg-primary">{{ ucfirst($calendar->type) }}</span>
                    </div>
                    
                    <p class="text-muted mb-3">{{ $calendar->description ?? 'No description available' }}</p>
                    
                    <div class="mb-3">
                        <strong><i class="fas fa-list me-2"></i>Total Medications:</strong> 
                        <span class="badge bg-success">{{ count($calendar->schedule ?? []) }}</span>
                    </div>

                    <div class="mb-3">
                        <strong><i class="fas fa-clock me-2"></i>Duration:</strong>
                        @if($calendar->schedule && count($calendar->schedule) > 0)
                            @php
                                $maxWeek = max(array_column($calendar->schedule, 'week'));
                            @endphp
                            <span>{{ $maxWeek }} weeks</span>
                        @else
                            <span>N/A</span>
                        @endif
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('medication-calendars.show', $calendar) }}" class="btn btn-agri flex-fill">
                            <i class="fas fa-eye me-2"></i>View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="agri-card">
                <div class="agri-card-body text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No medication calendars available</p>
                </div>
            </div>
        </div>
    @endforelse
</div>
@endsection

