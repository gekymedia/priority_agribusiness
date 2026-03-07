<?php $__env->startSection('title', 'Egg Production'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title">Egg Production Records</h1>
    <p class="page-subtitle">Track daily egg production from your layer batches</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="<?php echo e(route('egg-productions.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Production Record
        </a>
        <a href="<?php echo e(route('egg-productions.bulk-import')); ?>" class="btn btn-outline-primary ms-2">
            <i class="fas fa-file-import me-2"></i>Bulk Import
        </a>
    </div>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Batch</th>
                        <th>Farm</th>
                        <th>Eggs Collected</th>
                        <th>Cracked/Damaged</th>
                        <th>Used Internal</th>
                        <th>Available</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $productions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $production): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($production->date->format('M d, Y')); ?></td>
                        <td><?php echo e($production->birdBatch->batch_code ?? 'N/A'); ?></td>
                        <td><?php echo e($production->birdBatch->farm->name ?? 'N/A'); ?></td>
                        <td><strong><?php echo e(number_format($production->eggs_collected)); ?></strong></td>
                        <td><?php echo e(number_format($production->cracked_or_damaged)); ?></td>
                        <td><?php echo e(number_format($production->eggs_used_internal)); ?></td>
                        <td>
                            <span class="badge bg-success">
                                <?php echo e(number_format($production->eggs_collected - $production->cracked_or_damaged - $production->eggs_used_internal)); ?>

                            </span>
                        </td>
                        <td>
                            <a href="<?php echo e(route('egg-productions.show', $production)); ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo e(route('egg-productions.edit', $production)); ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo e(route('egg-productions.destroy', $production)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this record?');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No egg production records found</p>
                            <a href="<?php echo e(route('egg-productions.create')); ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add First Record
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($productions->hasPages()): ?>
        <div class="mt-4">
            <?php echo e($productions->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projects\priority_agribusiness\resources\views/egg-productions/index.blade.php ENDPATH**/ ?>