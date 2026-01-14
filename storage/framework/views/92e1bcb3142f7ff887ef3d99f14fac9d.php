<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Welcome back, <?php echo e(auth()->user()->name); ?>! Here's an overview of your agribusiness.</p>
</div>

<!-- Stats Overview -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h6 class="text-muted mb-2" style="font-size: 0.875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Total Farms</h6>
                    <h2 class="mb-0" style="font-size: 2.5rem; font-weight: 800; color: var(--primary);"><?php echo e(\App\Models\Farm::count()); ?></h2>
                </div>
                <div class="stat-icon" style="background: linear-gradient(135deg, rgba(46, 125, 50, 0.1), rgba(139, 195, 74, 0.1));">
                    <i class="fas fa-tractor" style="color: var(--primary);"></i>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <span class="badge bg-success bg-opacity-10 text-success">
                    <i class="fas fa-arrow-up me-1"></i>Active
                </span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h6 class="text-muted mb-2" style="font-size: 0.875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Bird Batches</h6>
                    <h2 class="mb-0" style="font-size: 2.5rem; font-weight: 800; color: var(--accent);"><?php echo e(\App\Models\BirdBatch::count()); ?></h2>
                </div>
                <div class="stat-icon" style="background: linear-gradient(135deg, rgba(139, 195, 74, 0.1), rgba(76, 175, 80, 0.1));">
                    <i class="fas fa-dove" style="color: var(--accent);"></i>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <span class="badge bg-success bg-opacity-10 text-success">
                    <i class="fas fa-check-circle me-1"></i><?php echo e(\App\Models\BirdBatch::where('status', 'active')->count()); ?> Active
                </span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h6 class="text-muted mb-2" style="font-size: 0.875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Plantings</h6>
                    <h2 class="mb-0" style="font-size: 2.5rem; font-weight: 800; color: var(--secondary);"><?php echo e(\App\Models\Planting::count()); ?></h2>
                </div>
                <div class="stat-icon" style="background: linear-gradient(135deg, rgba(255, 152, 0, 0.1), rgba(255, 193, 7, 0.1));">
                    <i class="fas fa-leaf" style="color: var(--secondary);"></i>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <span class="badge bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-seedling me-1"></i><?php echo e(\App\Models\Planting::where('status', 'growing')->count()); ?> Growing
                </span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h6 class="text-muted mb-2" style="font-size: 0.875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Pending Tasks</h6>
                    <h2 class="mb-0" style="font-size: 2.5rem; font-weight: 800; color: var(--danger);"><?php echo e(\App\Models\Task::where('status', 'pending')->count()); ?></h2>
                </div>
                <div class="stat-icon" style="background: linear-gradient(135deg, rgba(244, 67, 54, 0.1), rgba(239, 83, 80, 0.1));">
                    <i class="fas fa-tasks" style="color: var(--danger);"></i>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <span class="badge bg-danger bg-opacity-10 text-danger">
                    <i class="fas fa-clock me-1"></i>Requires Attention
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Additional Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="agri-card">
            <div class="agri-card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1">Total Houses</h6>
                        <h3 class="mb-0"><?php echo e(\App\Models\House::count()); ?></h3>
                    </div>
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, rgba(46, 125, 50, 0.1), rgba(139, 195, 74, 0.1)); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-home fa-2x" style="color: var(--primary);"></i>
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
                        <h6 class="text-muted mb-1">Total Fields</h6>
                        <h3 class="mb-0"><?php echo e(\App\Models\Field::count()); ?></h3>
                    </div>
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, rgba(139, 195, 74, 0.1), rgba(76, 175, 80, 0.1)); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-border-all fa-2x" style="color: var(--accent);"></i>
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
                        <h6 class="text-muted mb-1">Total Birds</h6>
                        <h3 class="mb-0"><?php echo e(\App\Models\BirdBatch::sum('quantity_arrived')); ?></h3>
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
                        <h6 class="text-muted mb-1">Completed Tasks</h6>
                        <h3 class="mb-0"><?php echo e(\App\Models\Task::where('status', 'done')->count()); ?></h3>
                    </div>
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, rgba(76, 175, 80, 0.1), rgba(139, 195, 74, 0.1)); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check-circle fa-2x" style="color: var(--success);"></i>
                    </div>
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
                    <?php
                        $recentBatches = \App\Models\BirdBatch::latest()->take(5)->get();
                        $recentPlantings = \App\Models\Planting::latest()->take(5)->get();
                        $recentTasks = \App\Models\Task::latest()->take(5)->get();
                    ?>

                    <?php if($recentBatches->count() > 0): ?>
                        <?php $__currentLoopData = $recentBatches->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="list-group-item border-0 px-0 py-3">
                            <div class="d-flex align-items-center">
                                <div style="width: 45px; height: 45px; background: linear-gradient(135deg, rgba(46, 125, 50, 0.1), rgba(139, 195, 74, 0.1)); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                    <i class="fas fa-dove" style="color: var(--primary);"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">New Bird Batch: <?php echo e($batch->batch_code); ?></h6>
                                    <small class="text-muted"><?php echo e($batch->created_at->diffForHumans()); ?></small>
                                </div>
                                <span class="badge bg-success bg-opacity-10 text-success"><?php echo e($batch->status); ?></span>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>

                    <?php if($recentPlantings->count() > 0): ?>
                        <?php $__currentLoopData = $recentPlantings->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $planting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="list-group-item border-0 px-0 py-3">
                            <div class="d-flex align-items-center">
                                <div style="width: 45px; height: 45px; background: linear-gradient(135deg, rgba(255, 152, 0, 0.1), rgba(255, 193, 7, 0.1)); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                    <i class="fas fa-leaf" style="color: var(--secondary);"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">New Planting: <?php echo e($planting->crop_name); ?></h6>
                                    <small class="text-muted"><?php echo e($planting->created_at->diffForHumans()); ?></small>
                                </div>
                                <span class="badge bg-warning bg-opacity-10 text-warning"><?php echo e($planting->status); ?></span>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>

                    <?php if($recentBatches->count() == 0 && $recentPlantings->count() == 0): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No recent activity</p>
                        </div>
                    <?php endif; ?>
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
                    <a href="<?php echo e(route('farms.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add New Farm
                    </a>
                    <a href="<?php echo e(route('batches.create')); ?>" class="btn btn-success">
                        <i class="fas fa-dove me-2"></i>Add Bird Batch
                    </a>
                    <a href="<?php echo e(route('plantings.create')); ?>" class="btn btn-warning">
                        <i class="fas fa-seedling me-2"></i>New Planting
                    </a>
                    <a href="<?php echo e(route('tasks.create')); ?>" class="btn btn-info">
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
                <?php
                    $pendingTasks = \App\Models\Task::where('status', 'pending')->orderBy('due_date', 'asc')->take(5)->get();
                ?>

                <?php if($pendingTasks->count() > 0): ?>
                    <div class="list-group list-group-flush">
                        <?php $__currentLoopData = $pendingTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="list-group-item border-0 px-0 py-2">
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1" style="font-size: 0.9rem;"><?php echo e($task->title); ?></h6>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?php if($task->due_date): ?>
                                            <?php echo e(\Carbon\Carbon::parse($task->due_date)->format('M d, Y')); ?>

                                        <?php else: ?>
                                            No due date
                                        <?php endif; ?>
                                    </small>
                                </div>
                                <span class="badge bg-<?php echo e($task->priority == 'high' ? 'danger' : ($task->priority == 'medium' ? 'warning' : 'info')); ?> bg-opacity-10 text-<?php echo e($task->priority == 'high' ? 'danger' : ($task->priority == 'medium' ? 'warning' : 'info')); ?>">
                                    <?php echo e(ucfirst($task->priority)); ?>

                                </span>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="mt-3">
                        <a href="<?php echo e(route('tasks.index')); ?>" class="btn btn-sm btn-outline-primary w-100">
                            View All Tasks <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <p class="text-muted mb-0">No pending tasks!</p>
                    </div>
                <?php endif; ?>
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
                <?php
                    $farms = \App\Models\Farm::with(['houses', 'fields'])->get();
                ?>

                <?php if($farms->count() > 0): ?>
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
                                <?php $__currentLoopData = $farms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $farm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e($farm->name); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo e($farm->farm_type == 'poultry' ? 'primary' : ($farm->farm_type == 'crop' ? 'success' : 'warning')); ?> bg-opacity-10 text-<?php echo e($farm->farm_type == 'poultry' ? 'primary' : ($farm->farm_type == 'crop' ? 'success' : 'warning')); ?>">
                                            <?php echo e(ucfirst($farm->farm_type)); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($farm->location ?? 'N/A'); ?></td>
                                    <td><?php echo e($farm->houses->count()); ?></td>
                                    <td><?php echo e($farm->fields->count()); ?></td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-10 text-success">Active</span>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('farms.show', $farm)); ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-tractor fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-3">No farms registered yet</p>
                        <a href="<?php echo e(route('farms.create')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create Your First Farm
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projects\priority_agribusiness\resources\views/dashboard.blade.php ENDPATH**/ ?>