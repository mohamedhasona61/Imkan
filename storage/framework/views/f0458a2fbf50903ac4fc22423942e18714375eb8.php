<div class="booking-review">
    <h4 class="booking-review-title"><?php echo e(__('Your Information')); ?></h4>
    <div class="booking-review-content">
        <div class="review-section">
            <div class="info-form">
                <ul>
                    <li class="info-first-name">
                        <div class="label"><?php echo e(__('First name')); ?></div>
                        <div class="val"><?php echo e($booking->first_name); ?></div>
                    </li>
                    <li class="info-last-name">
                        <div class="label"><?php echo e(__('Last name')); ?></div>
                        <div class="val"><?php echo e($booking->last_name); ?></div>
                    </li>
                    <?php if($booking->Email1=null): ?>
                    <li class="info-email">
                        <div class="label"><?php echo e(__('Email')); ?></div>
                        <div class="val"><?php echo e($booking->email); ?></div>
                    </li>
                    <?php endif; ?>
                    <li class="info-phone">
                        <div class="label"><?php echo e(__('Phone')); ?></div>
                        <div class="val"><?php echo e($booking->phone); ?></div>
                    </li>
                    
                    <?php if($booking->customer_notes!=null): ?>
        
                    <li class="info-notes">
                        <div class="label"><?php echo e(__('Special Requirements')); ?></div>
                        <div class="val"><?php echo e($booking->customer_notes); ?></div>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /www/wwwroot/nileview.dokkan.design/v1/themes/BC/Booking/Views/frontend/booking/booking-customer-info.blade.php ENDPATH**/ ?>