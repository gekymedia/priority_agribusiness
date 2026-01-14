<?php $__env->startSection('title', 'Profile Settings'); ?>

<?php $__env->startSection('content'); ?>
<div class="profile-page">
<div class="page-header">
    <h1 class="page-title">Profile Settings</h1>
    <p class="page-subtitle">Manage your account information and preferences</p>
</div>

<div class="row g-4">
    <!-- Profile Information -->
    <div class="col-lg-8">
        <div class="agri-card mb-4">
            <div class="agri-card-header">
                <h3><i class="fas fa-user me-2"></i>Profile Information</h3>
            </div>
            <div class="agri-card-body">
                <form method="POST" action="<?php echo e(route('profile.update')); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">
                                <i class="fas fa-user me-2"></i>Full Name
                            </label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="name" 
                                   name="name" 
                                   value="<?php echo e(old('name', auth()->user()->name)); ?>" 
                                   required>
                            <?php $__errorArgs = ['name'];
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
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email Address
                            </label>
                            <input type="email" 
                                   class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="email" 
                                   name="email" 
                                   value="<?php echo e(old('email', auth()->user()->email)); ?>" 
                                   required>
                            <?php $__errorArgs = ['email'];
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
                            <label for="role" class="form-label">
                                <i class="fas fa-user-tag me-2"></i>Role
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="role" 
                                   value="<?php echo e(ucfirst(auth()->user()->role ?? 'User')); ?>" 
                                   disabled
                                   style="background: var(--neutral);">
                            <small class="text-muted">Role cannot be changed</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fas fa-calendar me-2"></i>Member Since
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   value="<?php echo e(auth()->user()->created_at->format('F d, Y')); ?>" 
                                   disabled
                                   style="background: var(--neutral);">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="agri-card">
            <div class="agri-card-header" style="background: linear-gradient(135deg, rgba(244, 67, 54, 0.9), rgba(239, 83, 80, 0.9));">
                <h3><i class="fas fa-lock me-2"></i>Change Password</h3>
            </div>
            <div class="agri-card-body">
                <form method="POST" action="<?php echo e(route('profile.password.update')); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="current_password" class="form-label">
                                <i class="fas fa-key me-2"></i>Current Password
                            </label>
                            <input type="password" 
                                   class="form-control <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="current_password" 
                                   name="current_password" 
                                   required>
                            <?php $__errorArgs = ['current_password'];
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
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>New Password
                            </label>
                            <input type="password" 
                                   class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="password" 
                                   name="password" 
                                   required>
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted">Must be at least 8 characters</small>
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock me-2"></i>Confirm New Password
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-key me-2"></i>Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Account Summary -->
    <div class="col-lg-4">
        <div class="agri-card mb-4">
            <div class="agri-card-header">
                <h3><i class="fas fa-info-circle me-2"></i>Account Summary</h3>
            </div>
            <div class="agri-card-body">
                <div class="text-center mb-4">
                    <div style="width: 100px; height: 100px; background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; box-shadow: 0 10px 30px rgba(46, 125, 50, 0.3);">
                        <span style="color: white; font-size: 2.5rem; font-weight: 700;"><?php echo e(substr(auth()->user()->name, 0, 1)); ?></span>
                    </div>
                    <h4 class="mb-1"><?php echo e(auth()->user()->name); ?></h4>
                    <p class="text-muted mb-0"><?php echo e(auth()->user()->email); ?></p>
                </div>

                <div class="list-group list-group-flush">
                    <div class="list-group-item border-0 px-0 py-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Role:</span>
                            <strong><?php echo e(ucfirst(auth()->user()->role ?? 'User')); ?></strong>
                        </div>
                    </div>
                    <div class="list-group-item border-0 px-0 py-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Member Since:</span>
                            <strong><?php echo e(auth()->user()->created_at->format('M Y')); ?></strong>
                        </div>
                    </div>
                    <div class="list-group-item border-0 px-0 py-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Last Updated:</span>
                            <strong><?php echo e(auth()->user()->updated_at->diffForHumans()); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="agri-card">
            <div class="agri-card-header">
                <h3><i class="fas fa-chart-bar me-2"></i>Your Activity</h3>
            </div>
            <div class="agri-card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tasks Created:</span>
                        <strong><?php echo e(\App\Models\Task::where('created_by', auth()->id())->count()); ?></strong>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 10px;">
                        <div class="progress-bar bg-primary" 
                             style="width: <?php echo e(min(100, (\App\Models\Task::where('created_by', auth()->id())->count() / max(1, \App\Models\Task::count())) * 100)); ?>%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Completed Tasks:</span>
                        <strong><?php echo e(\App\Models\Task::where('created_by', auth()->id())->where('status', 'done')->count()); ?></strong>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 10px;">
                        <div class="progress-bar bg-success" 
                             style="width: <?php echo e(\App\Models\Task::where('created_by', auth()->id())->count() > 0 ? (\App\Models\Task::where('created_by', auth()->id())->where('status', 'done')->count() / \App\Models\Task::where('created_by', auth()->id())->count()) * 100 : 0); ?>%"></div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline-primary w-100">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projects\priority_agribusiness\resources\views/profile/index.blade.php ENDPATH**/ ?>