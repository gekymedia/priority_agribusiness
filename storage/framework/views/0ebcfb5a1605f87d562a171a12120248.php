<?php $__env->startSection('title', 'Farms'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title">Farms</h1>
    <p class="page-subtitle">Manage your agricultural farm locations</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="<?php echo e(route('farms.create')); ?>" class="btn btn-agri">
            <i class="fas fa-plus me-2"></i>Add Farm
        </a>
    </div>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <div class="table-responsive">
            <table class="table table-hover">
    <thead>
        <tr>
            <th>Name</th>
            <th>Location</th>
            <th>Type</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $farms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $farm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
                        <td><strong><?php echo e($farm->name); ?></strong></td>
            <td><?php echo e($farm->location); ?></td>
                        <td><span class="badge bg-primary"><?php echo e(ucfirst($farm->farm_type)); ?></span></td>
            <td>
                            <a href="<?php echo e(route('farms.show', $farm)); ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo e(route('farms.edit', $farm)); ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo e(route('farms.destroy', $farm)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this farm?');">
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
                        <td colspan="4" class="text-center py-5">
                            <i class="fas fa-tractor fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No farms registered yet</p>
                            <a href="<?php echo e(route('farms.create')); ?>" class="btn btn-agri">
                                <i class="fas fa-plus me-2"></i>Add First Farm
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
    </tbody>
</table>
        </div>

        <?php if($farms->hasPages()): ?>
        <div class="mt-4">
            <?php echo e($farms->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projects\priority_agribusiness\resources\views/farms/index.blade.php ENDPATH**/ ?>