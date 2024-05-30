
<?php $__env->startSection('content'); ?>
    <div class="b-container">
        <div class="b-panel">
            <h3>Hello!</h3>
            <p>This is email test.</p>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Email::layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/nileview.dokkan.design/v1/modules/Email/Views/emails/test.blade.php ENDPATH**/ ?>