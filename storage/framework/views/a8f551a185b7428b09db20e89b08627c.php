

<?php $__env->startSection('title', 'Bulk Import Egg Production'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title">Bulk Import Egg Production</h1>
    <p class="page-subtitle">Paste daily egg counts from your caretaker (one line per day, e.g. "19th January 0 eggs")</p>
</div>

<?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if(session('info')): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <?php echo e(session('info')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="agri-card">
    <div class="agri-card-body">
        <form method="POST" action="<?php echo e(route('egg-productions.bulk-import.process')); ?>">
            <?php echo csrf_field(); ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="bird_batch_id" class="form-label">
                        <i class="fas fa-dove me-2"></i>Bird Batch (layer)
                    </label>
                    <select name="bird_batch_id" id="bird_batch_id" class="form-select <?php $__errorArgs = ['bird_batch_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                        <option value="">Select batch for this production data</option>
                        <?php $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($batch->id); ?>" <?php echo e(old('bird_batch_id') == $batch->id ? 'selected' : ''); ?>>
                                <?php echo e($batch->batch_code); ?> - <?php echo e($batch->farm->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['bird_batch_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="col-md-6">
                    <label for="year" class="form-label">
                        <i class="fas fa-calendar me-2"></i>Year for dates without year
                    </label>
                    <input type="number" name="year" id="year" class="form-control <?php $__errorArgs = ['year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('year', date('Y'))); ?>" min="2020" max="2030">
                    <?php $__errorArgs = ['year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <small class="text-muted">Used when lines don't include a year (e.g. "19th January 0 eggs").</small>
                </div>

                <div class="col-12">
                    <label for="pasted_data" class="form-label">
                        <i class="fas fa-paste me-2"></i>Paste daily production (one line per day)
                    </label>
                    <textarea name="pasted_data" id="pasted_data" class="form-control font-monospace <?php $__errorArgs = ['pasted_data'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="14" placeholder="19th January 0 eggs&#10;20th January 0 eggs&#10;22nd January 3 eggs&#10;23rd January 1 damage egg&#10;..."><?php echo e(old('pasted_data')); ?></textarea>
                    <?php $__errorArgs = ['pasted_data'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <small class="text-muted">Supported: "X eggs", "X egg", "1 damage egg", "crack 1 egg", "7 eggs 1 broken". Dates already in the system for this batch are skipped.</small>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-file-import me-2"></i>Import Records
                </button>
                <a href="<?php echo e(route('egg-productions.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
                <a href="<?php echo e(route('egg-productions.create')); ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-plus me-2"></i>Add single record
                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projects\priority_agribusiness\resources\views/egg-productions/bulk-import.blade.php ENDPATH**/ ?>