
<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="row justify-content-center bravo-login-form-page bravo-login-page">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><?php echo e(__('Confirm Password')); ?></div>
                    <div class="card-body">
                        <div class="mb-4 text-sm text-gray-600">
                            <?php echo e(__('This is a secure area of the application. Please confirm your password before continuing.')); ?>

                        </div>

                        <?php echo $__env->make('Layout::admin.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                        <form method="POST" action="<?php echo e(route('password.confirm')); ?>">
                        <?php echo csrf_field(); ?>

                        <!-- Password -->
                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right"><?php echo e(__('Password')); ?></label>
                                <div class="col-md-6">
                                    <input autocomplete="current-password" id="password" type="password" class="form-control<?php echo e($errors->has('password') ? ' is-invalid' : ''); ?>" name="password"  required>
                                    <?php if($errors->has('password')): ?>
                                        <span class="invalid-feedback" role="alert">
                                            <strong><?php echo e($errors->first('password')); ?></strong>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex justify-end mt-4">
                                <div class="form-group row mb-0">
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            <?php echo e(__('Confirm')); ?>

                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\mamp\htdocs\flouka\Imkan\Imkan8-6\v1\resources\views/auth/confirm-password.blade.php ENDPATH**/ ?>