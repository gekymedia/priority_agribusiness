

<?php $__env->startSection('title', 'Employees'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title">Employees</h1>
    <p class="page-subtitle">Manage farm employees and their access levels</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="<?php echo e(route('employees.create')); ?>" class="btn btn-agri">
            <i class="fas fa-plus me-2"></i>Add Employee
        </a>
    </div>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Access Level</th>
                        <th>Farm/House</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><strong><?php echo e($employee->employee_id); ?></strong></td>
                        <td><?php echo e($employee->full_name); ?></td>
                        <td><?php echo e($employee->email); ?></td>
                        <td><?php echo e($employee->phone ?? 'N/A'); ?></td>
                        <td>
                            <?php
                                $badgeColors = [
                                    'admin' => 'danger',
                                    'manager' => 'warning',
                                    'caretaker' => 'primary',
                                    'viewer' => 'secondary'
                                ];
                            ?>
                            <span class="badge bg-<?php echo e($badgeColors[$employee->access_level] ?? 'secondary'); ?>">
                                <?php echo e(ucfirst($employee->access_level)); ?>

                            </span>
                        </td>
                        <td>
                            <?php if($employee->farm): ?>
                                <span class="badge bg-info"><?php echo e($employee->farm->name); ?></span>
                            <?php elseif($employee->house): ?>
                                <span class="badge bg-info"><?php echo e($employee->house->name); ?></span>
                            <?php else: ?>
                                <span class="text-muted">Not assigned</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($employee->is_active): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo e(route('employees.show', $employee)); ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo e(route('employees.edit', $employee)); ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo e(route('employees.destroy', $employee)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this employee?');">
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
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No employees registered yet</p>
                            <a href="<?php echo e(route('employees.create')); ?>" class="btn btn-agri">
                                <i class="fas fa-plus me-2"></i>Add First Employee
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($employees->hasPages()): ?>
        <div class="mt-4">
            <?php echo e($employees->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projects\priority_agribusiness\resources\views/employees/index.blade.php ENDPATH**/ ?>