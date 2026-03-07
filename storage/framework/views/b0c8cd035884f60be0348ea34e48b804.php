

<?php $__env->startSection('title', 'Bird Mortality Records'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Bird Mortality Records</h1>
        <p class="page-subtitle">Track daily mortality and culling for bird batches</p>
    </div>
    <a href="<?php echo e(route('bird-mortality.create')); ?>" class="btn btn-primary">
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
                    <h3 class="mb-0 text-danger"><?php echo e(number_format($totalMortality)); ?></h3>
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
                    <h3 class="mb-0 text-warning"><?php echo e(number_format($totalCulled)); ?></h3>
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
                    <?php $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($batch->id); ?>" <?php echo e(request('batch_id') == $batch->id ? 'selected' : ''); ?>>
                            <?php echo e($batch->batch_code); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Date From</label>
                <input type="date" name="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Date To</label>
                <input type="date" name="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
                <a href="<?php echo e(route('bird-mortality.index')); ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Clear
                </a>
            </div>
        </form>
    </div>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

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
                    <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e(\Carbon\Carbon::parse($record->record_date)->format('M d, Y')); ?></td>
                            <td>
                                <strong><?php echo e($record->birdBatch->batch_code ?? 'N/A'); ?></strong>
                            </td>
                            <td>
                                <?php if($record->birdBatch && $record->birdBatch->house): ?>
                                    <?php echo e($record->birdBatch->house->name); ?>

                                    <small class="text-muted d-block"><?php echo e($record->birdBatch->house->farm->name ?? ''); ?></small>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($record->mortality_count > 0): ?>
                                    <span class="badge bg-danger"><?php echo e($record->mortality_count); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">0</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($record->cull_count > 0): ?>
                                    <span class="badge bg-warning text-dark"><?php echo e($record->cull_count); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">0</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($record->feed_used_kg ? number_format($record->feed_used_kg, 1) : '—'); ?></td>
                            <td><?php echo e($record->water_used_litres ? number_format($record->water_used_litres, 1) : '—'); ?></td>
                            <td><?php echo e($record->average_weight_kg ? number_format($record->average_weight_kg, 2) . ' kg' : '—'); ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="<?php echo e(route('bird-mortality.edit', $record)); ?>" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?php echo e(route('bird-mortality.destroy', $record)); ?>" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this record?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                No mortality records found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($records->hasPages()): ?>
            <div class="mt-3">
                <?php echo e($records->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projects\priority_agribusiness\resources\views/bird-mortality/index.blade.php ENDPATH**/ ?>