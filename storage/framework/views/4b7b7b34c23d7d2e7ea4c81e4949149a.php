<?php $__env->startSection('title', 'Employees & Users'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title">Employees & Users</h1>
    <p class="page-subtitle">Manage system access for employees and users</p>
</div>

<?php if($pendingCount > 0): ?>
<div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
    <i class="fas fa-user-clock fa-2x me-3"></i>
    <div class="flex-grow-1">
        <strong><?php echo e($pendingCount); ?></strong> employee(s) pending approval. They cannot log in until you approve them.
    </div>
    <a href="<?php echo e(route('employees.index', ['status_filter' => 'pending', 'type_filter' => 'employees'])); ?>" class="btn btn-warning btn-sm">View pending</a>
</div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?php echo e(route('employees.create')); ?>" class="btn btn-agri">
            <i class="fas fa-plus me-2"></i>Add Employee
        </a>
        
        <div class="btn-group">
            <a href="<?php echo e(route('employees.index', ['type_filter' => 'all'])); ?>" 
               class="btn btn-outline-primary <?php echo e($typeFilter === 'all' ? 'active' : ''); ?>">
                All (<?php echo e($employeeCount + $userCount); ?>)
            </a>
            <a href="<?php echo e(route('employees.index', ['type_filter' => 'employees'])); ?>" 
               class="btn btn-outline-primary <?php echo e($typeFilter === 'employees' ? 'active' : ''); ?>">
                <i class="fas fa-id-badge me-1"></i>Employees (<?php echo e($employeeCount); ?>)
            </a>
            <a href="<?php echo e(route('employees.index', ['type_filter' => 'users'])); ?>" 
               class="btn btn-outline-primary <?php echo e($typeFilter === 'users' ? 'active' : ''); ?>">
                <i class="fas fa-user me-1"></i>Users (<?php echo e($userCount); ?>)
            </a>
        </div>

        <?php if($typeFilter === 'employees' || $typeFilter === 'all'): ?>
        <div class="btn-group">
            <a href="<?php echo e(route('employees.index', ['type_filter' => $typeFilter, 'status_filter' => 'pending'])); ?>" 
               class="btn btn-outline-warning <?php echo e($statusFilter === 'pending' ? 'active' : ''); ?>">
                <i class="fas fa-user-clock me-1"></i>Pending
            </a>
            <a href="<?php echo e(route('employees.index', ['type_filter' => $typeFilter, 'status_filter' => 'approved'])); ?>" 
               class="btn btn-outline-success <?php echo e($statusFilter === 'approved' ? 'active' : ''); ?>">
                <i class="fas fa-user-check me-1"></i>Approved
            </a>
        </div>
        <?php endif; ?>

        <?php if($statusFilter): ?>
        <a href="<?php echo e(route('employees.index', ['type_filter' => $typeFilter])); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-times me-1"></i>Clear Filter
        </a>
        <?php endif; ?>
    </div>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <?php if($record->record_type === 'employee'): ?>
                                <span class="badge bg-primary"><i class="fas fa-id-badge me-1"></i>Employee</span>
                            <?php else: ?>
                                <span class="badge bg-info"><i class="fas fa-user me-1"></i>User</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo e($record->record_type === 'employee' ? $record->employee_id : 'USR-' . $record->id); ?></strong>
                        </td>
                        <td><?php echo e($record->display_name); ?></td>
                        <td><?php echo e($record->email); ?></td>
                        <td><?php echo e($record->phone ?? 'N/A'); ?></td>
                        <td>
                            <?php
                                $roleBadgeColors = [
                                    'Admin' => 'danger',
                                    'Poultry Farm Manager' => 'warning',
                                    'Crop Farms Manager' => 'success',
                                ];
                            ?>
                            <span class="badge bg-<?php echo e($roleBadgeColors[$record->display_role] ?? 'secondary'); ?>">
                                <?php echo e($record->display_role); ?>

                            </span>
                        </td>
                        <td>
                            <?php if($record->status === 'approved'): ?>
                                <span class="badge bg-success">Approved</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($record->is_active): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                                $currentUser = auth()->user();
                                $canImpersonate = ($currentUser instanceof \App\Models\User) || 
                                                 ($currentUser instanceof \App\Models\Employee && $currentUser->isAdmin());
                            ?>
                            
                            <?php if($record->record_type === 'employee'): ?>
                                <?php if($record->status === 'pending'): ?>
                                    <form action="<?php echo e(route('employees.approve', $record)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="btn btn-sm btn-success" title="Approve employee">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php
                                    $isNotSelf = !($currentUser instanceof \App\Models\Employee && $currentUser->id === $record->id);
                                ?>
                                
                                <?php if($canImpersonate && $isNotSelf && $record->status === 'approved'): ?>
                                    <form action="<?php echo e(route('impersonate.start', $record)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-dark" title="Impersonate this employee">
                                            <i class="fas fa-user-secret"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <a href="<?php echo e(route('employees.show', $record)); ?>" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('employees.edit', $record)); ?>" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('employees.destroy', $record)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <?php
                                    $isNotSelf = !($currentUser instanceof \App\Models\User && $currentUser->id === $record->id);
                                ?>
                                
                                <?php if($canImpersonate && $isNotSelf): ?>
                                    <form action="<?php echo e(route('impersonate.user', $record)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-dark" title="Impersonate this user">
                                            <i class="fas fa-user-secret"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <a href="<?php echo e(route('users.show', $record)); ?>" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('users.edit', $record)); ?>" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('users.destroy', $record)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No employees or users found</p>
                            <a href="<?php echo e(route('employees.create')); ?>" class="btn btn-agri">
                                <i class="fas fa-plus me-2"></i>Add First Employee
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($records->hasPages()): ?>
        <div class="mt-4">
            <?php echo e($records->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projects\priority_agribusiness\resources\views/employees/index.blade.php ENDPATH**/ ?>