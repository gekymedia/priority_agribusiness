@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    @php
        $user = auth()->user();
        $userName = $user instanceof \App\Models\Employee ? $user->full_name : ($user->name ?? 'User');
    @endphp
    <p class="page-subtitle">Welcome back, {{ $userName }}! Here's an overview of your agribusiness.</p>
</div>

<!-- Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="agri-card">
            <div class="agri-card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1">Eggs Produced Today</h6>
                        <h3 class="mb-0">{{ $eggsProducedToday ?? 0 }}</h3>
                    </div>
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, rgba(46, 125, 50, 0.1), rgba(139, 195, 74, 0.1)); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-egg fa-2x" style="color: var(--primary);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="agri-card">
            <div class="agri-card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1">Eggs Sold Today</h6>
                        <h3 class="mb-0">{{ $eggsSoldToday ?? 0 }}</h3>
                    </div>
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, rgba(139, 195, 74, 0.1), rgba(76, 175, 80, 0.1)); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-shopping-basket fa-2x" style="color: var(--accent);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="agri-card">
            <div class="agri-card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1">Remaining Birds</h6>
                        <h3 class="mb-0">{{ number_format($totalRemainingBirds ?? 0) }}</h3>
                    </div>
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, rgba(255, 152, 0, 0.1), rgba(255, 193, 7, 0.1)); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-dove fa-2x" style="color: var(--secondary);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="agri-card">
            <div class="agri-card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1">Eggs In Stock</h6>
                        <h3 class="mb-0">{{ number_format($totalEggsInStock ?? 0) }}</h3>
                        <small class="text-muted">
                            {{ number_format($eggsInStockCrates ?? 0) }} crates + {{ number_format($eggsInStockLoose ?? 0) }} eggs ({{ $eggsPerCrate ?? 30 }}/crate)
                        </small>
                    </div>
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, rgba(76, 175, 80, 0.1), rgba(139, 195, 74, 0.1)); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-box-open fa-2x" style="color: var(--success);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Account & Finance Balances -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="agri-card">
            <div class="agri-card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1">Income & Expenditure Balance</h6>
                        <h3 class="mb-0 {{ ($incomeExpenditureBalance ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                            ₵{{ number_format($incomeExpenditureBalance ?? 0, 2) }}
                        </h3>
                        <small class="text-muted">Total recorded income minus expenditure</small>
                    </div>
                    <div style="width: 56px; height: 56px; background: linear-gradient(135deg, rgba(46, 125, 50, 0.12), rgba(139, 195, 74, 0.12)); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-balance-scale fa-xl" style="color: var(--primary);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="agri-card">
            <div class="agri-card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1">Bank Balance</h6>
                        @if(isset($bankBalance))
                            <h3 class="mb-0 text-primary">₵{{ number_format($bankBalance, 2) }}</h3>
                            <small class="text-muted">From Priority Bank</small>
                        @else
                            <p class="mb-0 text-muted small">Configure Priority Bank in Settings to show balance.</p>
                        @endif
                    </div>
                    <div style="width: 56px; height: 56px; background: linear-gradient(135deg, rgba(14, 165, 233, 0.12), rgba(56, 189, 248, 0.12)); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-university fa-xl" style="color: #0ea5e9;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Egg Production vs Sales Chart -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="agri-card">
            <div class="agri-card-header">
                <h3><i class="fas fa-chart-line me-2"></i>Egg Production vs Egg Sales</h3>
            </div>
            <div class="agri-card-body">
                <p class="text-muted small mb-3">Day-by-day for the last 31 days: production (eggs collected, net of damaged/internal use) vs eggs sold.</p>
                <div class="position-relative" style="height: 320px;">
                    <canvas id="eggProductionVsSalesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Row -->
<div class="row g-4">
    <!-- Recent Activity -->
    <div class="col-lg-8">
        <div class="agri-card">
            <div class="agri-card-header">
                <h3><i class="fas fa-clock me-2"></i>Recent Activity</h3>
            </div>
            <div class="agri-card-body">
                <div class="list-group list-group-flush">
                    @php
                        $recentBatches = \App\Models\BirdBatch::latest()->take(5)->get();
                        $recentPlantings = \App\Models\Planting::latest()->take(5)->get();
                        $recentTasks = \App\Models\Task::latest()->take(5)->get();
                    @endphp

                    @if($recentBatches->count() > 0)
                        @foreach($recentBatches->take(3) as $batch)
                        <div class="list-group-item border-0 px-0 py-3">
                            <div class="d-flex align-items-center">
                                <div style="width: 45px; height: 45px; background: linear-gradient(135deg, rgba(46, 125, 50, 0.1), rgba(139, 195, 74, 0.1)); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                    <i class="fas fa-dove" style="color: var(--primary);"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">New Bird Batch: {{ $batch->batch_code }}</h6>
                                    <small class="text-muted">{{ $batch->created_at->diffForHumans() }}</small>
                                </div>
                                <span class="badge bg-success bg-opacity-10 text-success">{{ $batch->status }}</span>
                            </div>
                        </div>
                        @endforeach
                    @endif

                    @if($recentPlantings->count() > 0)
                        @foreach($recentPlantings->take(2) as $planting)
                        <div class="list-group-item border-0 px-0 py-3">
                            <div class="d-flex align-items-center">
                                <div style="width: 45px; height: 45px; background: linear-gradient(135deg, rgba(255, 152, 0, 0.1), rgba(255, 193, 7, 0.1)); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                    <i class="fas fa-leaf" style="color: var(--secondary);"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">New Planting: {{ $planting->crop_name }}</h6>
                                    <small class="text-muted">{{ $planting->created_at->diffForHumans() }}</small>
                                </div>
                                <span class="badge bg-warning bg-opacity-10 text-warning">{{ $planting->status }}</span>
                            </div>
                        </div>
                        @endforeach
                    @endif

                    @if($recentBatches->count() == 0 && $recentPlantings->count() == 0)
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No recent activity</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Pending Tasks -->
    <div class="col-lg-4">
        <div class="agri-card mb-4">
            <div class="agri-card-header">
                <h3><i class="fas fa-bolt me-2"></i>Quick Actions</h3>
            </div>
            <div class="agri-card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('farms.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add New Farm
                    </a>
                    <a href="{{ route('batches.create') }}" class="btn btn-success">
                        <i class="fas fa-dove me-2"></i>Add Bird Batch
                    </a>
                    <a href="{{ route('plantings.create') }}" class="btn btn-warning">
                        <i class="fas fa-seedling me-2"></i>New Planting
                    </a>
                    <a href="{{ route('tasks.create') }}" class="btn btn-info">
                        <i class="fas fa-tasks me-2"></i>Create Task
                    </a>
                </div>
            </div>
        </div>

        <div class="agri-card">
            <div class="agri-card-header" style="background: linear-gradient(135deg, rgba(244, 67, 54, 0.9), rgba(239, 83, 80, 0.9));">
                <h3><i class="fas fa-exclamation-triangle me-2"></i>Pending Tasks</h3>
            </div>
            <div class="agri-card-body">
                @php
                    $pendingTasks = \App\Models\Task::where('status', 'pending')->orderBy('due_date', 'asc')->take(5)->get();
                @endphp

                @if($pendingTasks->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($pendingTasks as $task)
                        <div class="list-group-item border-0 px-0 py-2">
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1" style="font-size: 0.9rem;">{{ $task->title }}</h6>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        @if($task->due_date)
                                            {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}
                                        @else
                                            No due date
                                        @endif
                                    </small>
                                </div>
                                <span class="badge bg-{{ $task->priority == 'high' ? 'danger' : ($task->priority == 'medium' ? 'warning' : 'info') }} bg-opacity-10 text-{{ $task->priority == 'high' ? 'danger' : ($task->priority == 'medium' ? 'warning' : 'info') }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-primary w-100">
                            View All Tasks <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <p class="text-muted mb-0">No pending tasks!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Farm Overview -->
<div class="row g-4 mt-2">
    <div class="col-12">
        <div class="agri-card">
            <div class="agri-card-header">
                <h3><i class="fas fa-tractor me-2"></i>Farm Overview</h3>
            </div>
            <div class="agri-card-body">
                @php
                    $farms = \App\Models\Farm::with(['houses', 'fields'])->get();
                @endphp

                @if($farms->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Farm Name</th>
                                    <th>Type</th>
                                    <th>Location</th>
                                    <th>Houses</th>
                                    <th>Fields</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($farms as $farm)
                                <tr>
                                    <td>
                                        <strong>{{ $farm->name }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $farm->farm_type == 'poultry' ? 'primary' : ($farm->farm_type == 'crop' ? 'success' : 'warning') }} bg-opacity-10 text-{{ $farm->farm_type == 'poultry' ? 'primary' : ($farm->farm_type == 'crop' ? 'success' : 'warning') }}">
                                            {{ ucfirst($farm->farm_type) }}
                                        </span>
                                    </td>
                                    <td>{{ $farm->location ?? 'N/A' }}</td>
                                    <td>{{ $farm->houses->count() }}</td>
                                    <td>{{ $farm->fields->count() }}</td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-10 text-success">Active</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('farms.show', $farm) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-tractor fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-3">No farms registered yet</p>
                        <a href="{{ route('farms.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create Your First Farm
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function() {
    var chartData = @json($chart ?? ['labels' => [], 'production' => [], 'sales' => []]);
    var ctx = document.getElementById('eggProductionVsSalesChart');
    if (!ctx || !chartData.labels.length) return;
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: 'Egg Production',
                    data: chartData.production,
                    borderColor: 'rgb(46, 125, 50)',
                    backgroundColor: 'rgba(46, 125, 50, 0.1)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Egg Sales',
                    data: chartData.sales,
                    borderColor: 'rgb(255, 152, 0)',
                    backgroundColor: 'rgba(255, 152, 0, 0.1)',
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Eggs' } }
            }
        }
    });
})();
</script>
@endsection
