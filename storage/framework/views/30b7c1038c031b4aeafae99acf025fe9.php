<?php $__env->startSection('title', 'Expenses'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title">Expenses</h1>
    <p class="page-subtitle">Track all farm expenses</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="<?php echo e(route('expenses.create')); ?>" class="btn btn-primary me-2">
            <i class="fas fa-plus me-2"></i>Add Expense
        </a>
        <a href="<?php echo e(route('expense-categories.index')); ?>" class="btn btn-outline-primary">
            <i class="fas fa-tags me-2"></i>Manage Categories
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
                        <th>Farm</th>
                        <th>Batch</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($expense->date->format('M d, Y')); ?></td>
                        <td><?php echo e($expense->farm->name ?? 'N/A'); ?></td>
                        <td><?php echo e($expense->birdBatch->batch_code ?? 'General'); ?></td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                <?php echo e($expense->category->name ?? $expense->category ?? 'N/A'); ?>

                            </span>
                        </td>
                        <td><?php echo e(\Illuminate\Support\Str::limit($expense->description ?? 'N/A', 30)); ?></td>
                        <td><strong class="text-danger">₵<?php echo e(number_format($expense->amount, 2)); ?></strong></td>
                        <td>
                            <a href="<?php echo e(route('expenses.show', $expense)); ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo e(route('expenses.edit', $expense)); ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo e(route('expenses.destroy', $expense)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this expense?');">
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
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No expenses recorded</p>
                            <a href="<?php echo e(route('expenses.create')); ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add First Expense
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($expenses->hasPages()): ?>
        <div class="mt-4">
            <?php echo e($expenses->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projects\priority_agribusiness\resources\views/expenses/index.blade.php ENDPATH**/ ?>