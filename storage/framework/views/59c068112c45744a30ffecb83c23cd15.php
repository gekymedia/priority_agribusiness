

<?php $__env->startSection('title', 'Payroll'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Payroll</h1>
        <p class="page-subtitle">Manage employee salary payments</p>
    </div>
    <a href="<?php echo e(route('payroll.create')); ?>" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>New Payroll
    </a>
</div>

<!-- Filter -->
<div class="agri-card mb-4">
    <div class="agri-card-body">
        <form class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Filter by Month</label>
                <input type="month" class="form-control" name="month" value="<?php echo e($month); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter me-2"></i>Filter
                </button>
            </div>
            <?php if($month): ?>
            <div class="col-md-2">
                <a href="<?php echo e(route('payroll.index')); ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Clear
                </a>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Payroll Table -->
<div class="agri-card">
    <div class="agri-card-header">
        <h3><i class="fas fa-money-check-alt me-2"></i>Payroll Records</h3>
    </div>
    <div class="agri-card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Period</th>
                        <th>Base Salary</th>
                        <th>Allowances</th>
                        <th>Deductions</th>
                        <th>Net Pay</th>
                        <th>Status</th>
                        <th>Paid At</th>
                        <th width="140">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $payrolls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payroll): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $badgeClass = match($payroll->status) {
                                'draft' => 'bg-secondary',
                                'approved' => 'bg-warning',
                                'paid' => 'bg-success',
                                default => 'bg-secondary'
                            };
                        ?>
                        <tr data-row-id="<?php echo e($payroll->id); ?>">
                            <td class="fw-bold"><?php echo e($payroll->employee->full_name); ?></td>
                            <td><?php echo e(\Carbon\Carbon::parse($payroll->pay_period)->format('M Y')); ?></td>
                            <td>GHS <?php echo e(number_format($payroll->base_salary, 2)); ?></td>
                            <td>GHS <?php echo e(number_format($payroll->allowances_total, 2)); ?></td>
                            <td>GHS <?php echo e(number_format($payroll->deductions_total, 2)); ?></td>
                            <td class="fw-bold text-success">GHS <?php echo e(number_format($payroll->net_pay, 2)); ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <select class="form-select form-select-sm payroll-status" 
                                            data-id="<?php echo e($payroll->id); ?>"
                                            data-current="<?php echo e($payroll->status); ?>"
                                            style="width: auto;">
                                        <option value="draft" <?php if($payroll->status === 'draft'): echo 'selected'; endif; ?>>Draft</option>
                                        <option value="approved" <?php if($payroll->status === 'approved'): echo 'selected'; endif; ?>>Approved</option>
                                        <option value="paid" <?php if($payroll->status === 'paid'): echo 'selected'; endif; ?>>Paid</option>
                                    </select>
                                    <span class="badge <?php echo e($badgeClass); ?> status-badge"><?php echo e(ucfirst($payroll->status)); ?></span>
                                    <div class="spinner-border spinner-border-sm text-secondary d-none status-spinner" role="status"></div>
                                </div>
                            </td>
                            <td class="paid-at-cell">
                                <?php echo e($payroll->paid_at ? $payroll->paid_at->format('Y-m-d H:i') : '—'); ?>

                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="<?php echo e(route('payroll.edit', $payroll)); ?>" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?php echo e(route('payroll.destroy', $payroll)); ?>" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this payroll record?')">
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
                                No payroll records found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($payrolls->hasPages()): ?>
            <div class="mt-3">
                <?php echo e($payrolls->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>

<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

<script>
document.addEventListener('DOMContentLoaded', function() {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function badgeClass(status) {
        return {
            'draft': 'bg-secondary',
            'approved': 'bg-warning',
            'paid': 'bg-success'
        }[status] || 'bg-secondary';
    }

    document.querySelectorAll('.payroll-status').forEach(function(sel) {
        sel.addEventListener('change', async function() {
            const id = this.dataset.id;
            const newStatus = this.value;
            const row = this.closest('tr');
            const spinner = row.querySelector('.status-spinner');
            const badge = row.querySelector('.status-badge');
            const paidAtCell = row.querySelector('.paid-at-cell');
            const oldStatus = this.dataset.current;

            this.disabled = true;
            spinner.classList.remove('d-none');

            try {
                const res = await fetch("<?php echo e(url('/payroll')); ?>/" + id + "/status", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: newStatus })
                });

                if (!res.ok) throw new Error('Request failed');
                const json = await res.json();

                badge.className = 'badge status-badge ' + badgeClass(json.status);
                badge.textContent = json.status_label;
                paidAtCell.textContent = json.paid_at ?? '—';
                this.dataset.current = json.status;
            } catch (e) {
                alert('Could not update status. Please try again.');
                this.value = oldStatus;
            } finally {
                spinner.classList.add('d-none');
                this.disabled = false;
            }
        });
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projects\priority_agribusiness\resources\views/payroll/index.blade.php ENDPATH**/ ?>