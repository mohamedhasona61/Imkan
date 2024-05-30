
<?php $__env->startSection('content'); ?>
    <div class="b-container">
        <div class="b-panel">
            <h3 class="email-headline"><strong><?php echo e(__('Hello :name',['name'=>$enquiry->name])); ?></strong></h3>
            <p><?php echo e(__('You got reply from vendor. ')); ?></p>
            <?php $service = $enquiry->service; ?>
            <?php if(!empty($service)): ?>
                <p><strong><?php echo e(__("Service:")); ?></strong> <a href="<?php echo e($service->getDetailUrl()); ?>"><?php echo e($service->title); ?></a></p>
                <p><strong><?php echo e(__("Your note:")); ?></strong> <?php echo e($enquiry->note); ?></p>
            <?php endif; ?>
            <p><?php echo e(__('Here is the message from vendor:')); ?></p>
            <p><?php echo clean($enquiry_reply->content); ?></p>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Email::layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/nileview.dokkan.design/v1/themes/Base/Booking/Views/emails/enquiry_reply.blade.php ENDPATH**/ ?>