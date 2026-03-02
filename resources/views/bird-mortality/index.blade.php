@extends('layouts.app')

@section('title', 'Bird Mortality Records')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Bird Mortality Records</h1>
        <p class="page-subtitle">Track daily mortality and culling for bird batches</p>
    </div>
    <a href="{{ route('bird-mortality.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Record
    </a>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="agri-card bg-danger bg-opacity-10">
            <div class="agri-card-body d-flex align-items-center">
                <div class="rounded-circle bg-danger bg-opacity-25 p-3 me-3">
                    <i class="fas fa-skull-crossbones fa-2x text-danger"></i>
                </div>
                <div>
                    <h3 class="mb-0 text-danger">{{ number_format($totalMortality) }}</h3>
                    <p class="mb-0 text-muted">Total Mortality</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="agri-card bg-warning bg-opacity-10">
            <div class="agri-card-body d-flex align-items-center">
                <div class="rounded-circle bg-warning bg-opacity-25 p-3 me-3">
                    <i class="fas fa-cut fa-2x text-warning"></i>
                </div>
                <div>
                    <h3 class="mb-0 text-warning">{{ number_format($totalCulled) }}</h3>
                    <p class="mb-0 text-muted">Total Culled</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="agri-card mb-4">
    <div class="agri-card-body">
        <form class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Batch</label>
                <select name="batch_id" class="form-select">
                    <option value="">All Batches</option>
                    @foreach($batches as $batch)
                        <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>
                            {{ $batch->batch_code }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Date From</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Date To</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
                <a href="{{ route('bird-mortality.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Clear
                </a>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Records Table -->
<div class="agri-card">
    <div class="agri-card-header">
        <h3><i class="fas fa-list me-2"></i>Mortality Records</h3>
    </div>
    <div class="agri-card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Batch</th>
                        <th>House</th>
                        <th>Mortality</th>
                        <th>Culled</th>
                        <th>Feed (kg)</th>
                        <th>Water (L)</th>
                        <th>Avg Weight</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($record->record_date)->format('M d, Y') }}</td>
                            <td>
                                <strong>{{ $record->birdBatch->batch_code ?? 'N/A' }}</strong>
                            </td>
                            <td>
                                @if($record->birdBatch && $record->birdBatch->house)
                                    {{ $record->birdBatch->house->name }}
                                    <small class="text-muted d-block">{{ $record->birdBatch->house->farm->name ?? '' }}</small>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($record->mortality_count > 0)
                                    <span class="badge bg-danger">{{ $record->mortality_count }}</span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td>
                                @if($record->cull_count > 0)
                                    <span class="badge bg-warning text-dark">{{ $record->cull_count }}</span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td>{{ $record->feed_used_kg ? number_format($record->feed_used_kg, 1) : '—' }}</td>
                            <td>{{ $record->water_used_litres ? number_format($record->water_used_litres, 1) : '—' }}</td>
                            <td>{{ $record->average_weight_kg ? number_format($record->average_weight_kg, 2) . ' kg' : '—' }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('bird-mortality.edit', $record) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('bird-mortality.destroy', $record) }}" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                No mortality records found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
            <div class="mt-3">
                {{ $records->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
