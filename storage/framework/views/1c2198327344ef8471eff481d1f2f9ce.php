<?php $__env->startSection('title', 'Egg Sales'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title">Egg Sales</h1>
    <p class="page-subtitle">Track all egg sales transactions</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="<?php echo e(route('egg-sales.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Record Sale
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
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Price/Unit</th>
                        <th>Total Amount</th>
                        <th>Buyer</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($sale->date->format('M d, Y')); ?></td>
                        <td><?php echo e($sale->birdBatch->batch_code ?? 'N/A'); ?></td>
                        <td><?php echo e($sale->birdBatch->farm->name ?? 'N/A'); ?></td>
                        <td><strong><?php echo e(number_format($sale->quantity_sold)); ?></strong></td>
                        <td><span class="badge bg-info"><?php echo e(ucfirst($sale->unit_type)); ?></span></td>
                        <td>₵<?php echo e(number_format($sale->price_per_unit, 2)); ?></td>
                        <td><strong class="text-success">₵<?php echo e(number_format($sale->quantity_sold * $sale->price_per_unit, 2)); ?></strong></td>
                        <td><?php echo e($sale->buyer_name ?? 'N/A'); ?></td>
                        <td>
                            <a href="<?php echo e(route('egg-sales.show', $sale)); ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo e(route('egg-sales.edit', $sale)); ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo e(route('egg-sales.destroy', $sale)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this sale?');">
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
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No egg sales recorded</p>
                            <a href="<?php echo e(route('egg-sales.create')); ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Record First Sale
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($sales->hasPages()): ?>
        <div class="mt-4">
            <?php echo e($sales->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projects\priority_agribusiness\resources\views/egg-sales/index.blade.php ENDPATH**/ ?>