

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center bravo-login-form-page bravo-login-page">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><?php echo e(__('Verify Your Email Address')); ?></div>
                <div class="card-body">
                    <?php if(session('resent')): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo e(__('A fresh verification link has been sent to your email address.')); ?>

                        </div>
                    <?php endif; ?>
                    <p>
                        <?php echo e(__('Before proceeding, please check your email for a verification link.')); ?>

                        <?php echo e(__('If you did not receive the email')); ?>,
                    </p>
                        <form action="<?php echo e(route('verification.send')); ?>" method="post">
                            <?php echo csrf_field(); ?>
                            <button class="btn btn-primary" type="submit"><?php echo e(__('click here to request another')); ?>.</button>
                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/nileview.dokkan.design/v1/resources/views/auth/verify.blade.php ENDPATH**/ ?>