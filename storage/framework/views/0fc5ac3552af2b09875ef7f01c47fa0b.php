<?php $__env->startSection('title', 'Expense Categories'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title">Expense Categories</h1>
    <p class="page-subtitle">Manage expense categories for better organization</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="<?php echo e(route('expense-categories.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Category
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
                        <th>Type</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><strong><?php echo e($category->name); ?></strong></td>
                        <td>
                            <span class="badge bg-<?php echo e($category->type == 'poultry' ? 'primary' : ($category->type == 'crop' ? 'success' : 'info')); ?> bg-opacity-10 text-<?php echo e($category->type == 'poultry' ? 'primary' : ($category->type == 'crop' ? 'success' : 'info')); ?>">
                                <?php echo e(ucfirst($category->type)); ?>

                            </span>
                        </td>
                        <td><?php echo e(\Illuminate\Support\Str::limit($category->description ?? 'N/A', 50)); ?></td>
                        <td>
                            <?php if($category->is_active): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo e(route('expense-categories.edit', $category)); ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo e(route('expense-categories.destroy', $category)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?');">
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
                        <td colspan="5" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No expense categories found</p>
                            <a href="<?php echo e(route('expense-categories.create')); ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create First Category
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($categories->hasPages()): ?>
        <div class="mt-4">
            <?php echo e($categories->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projects\priority_agribusiness\resources\views/expense-categories/index.blade.php ENDPATH**/ ?>