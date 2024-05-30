
<?php $__env->startSection('content'); ?>
    <h2 class="title-bar">
        <?php echo e(__("Two Factor Authentication")); ?>

    </h2>
    <?php echo $__env->make('admin.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="row">
        <div class="col-sm-8">
            <div class="panel">
                <div class="panel-title"><strong><?php echo e(__("Setup Two Factor Authentication")); ?></strong></div>
                <div class="panel-body">
                    <?php if(auth()->user()->two_factor_secret): ?>
                        <?php echo $__env->make('User::frontend.2fa.parts.info', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php else: ?>
                        <?php echo $__env->make('User::frontend.2fa.parts.setup', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.user', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\mamp\htdocs\flouka\Imkan\Imkan8-6\v1\themes/Base/User/Views/frontend/2fa/index.blade.php ENDPATH**/ ?>