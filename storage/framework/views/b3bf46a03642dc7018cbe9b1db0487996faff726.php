<?php
$translation = $service->translate();
$lang_local = app()->getLocale();
?>
<div class="b-panel-title"><?php echo e(__('Tour information')); ?></div>
<div class="b-table-wrap">
    <table class="b-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="label"><?php echo e(__('Booking Number')); ?></td>
            <td class="val">#<?php echo e($booking->id); ?></td>
        </tr>
        <tr>
            <td class="label"><?php echo e(__('Booking Status')); ?></td>
            <td class="val"><?php echo e($booking->statusName); ?></td>
        </tr>
        <?php if($booking->gatewayObj): ?>
        <tr>
            <td class="label"><?php echo e(__('Payment method')); ?></td>
            <td class="val"><?php echo e($booking->gateway); ?></td>
        </tr>
        <?php endif; ?>
        <?php if($booking->gatewayObj and $note = $booking->gatewayObj->getOption('payment_note')): ?>
        <tr>
            <td class="label"><?php echo e(__('Payment Note')); ?></td>
            <td class="val"><?php echo clean($note); ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <td class="label"><?php echo e(__('Tour name')); ?></td>
            <td class="val">
                <a href="<?php echo e($service->getDetailUrl()); ?>"><?php echo clean($translation->title); ?></a>
            </td>

        </tr>
        <tr>
            <?php if($translation->address): ?>
            <td class="label"><?php echo e(__('Address')); ?></td>
            <td class="val">
                <?php echo e($translation->address); ?>

            </td>
            <?php endif; ?>
        </tr>
        <?php if($booking->start_date && $booking->end_date): ?>

        <?php
        ?>
        <tr>
            <td class="label"><?php echo e(__('Start From')); ?></td>
            <td class="val"><?php echo e($start_from); ?></td>
        </tr>
        <tr>
            <td class="label"><?php echo e(__('End At')); ?></td>
            <td class="val"><?php echo e($end_at); ?></td>
        </tr>

        <tr>
            <td class="label"><?php echo e(__('Duration:')); ?></td>
            <td class="val">
                <?php echo e(human_time_diff($end_at,$start_from)); ?>

            </td>
        </tr>
        <?php endif; ?>

        <?php
        $person_types = $booking->getJsonMeta('person_types')
        ?>
        <?php if(!empty($person_types)): ?>
        <tr>
            <td class="label"><?php echo e(__("Guests")); ?>:</td>
            <td class="val">
                <strong><?php echo e($person_types[0]['number']); ?></strong>
            </td>
        </tr>
        <?php endif; ?>

        <?php
        $menus = $booking->getJsonMeta('menus');
        ?>

        <?php if(!empty($menus) && count($menus) > 0): ?>
        <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
        $menu = json_encode($menu);
        $menu = json_decode($menu);

        if ($menu->submenuId== $menu->itemId) {
        $item = \Modules\Core\Models\MenuTour::find($menu->submenuId);
        } else {
        $item = \Modules\Core\Models\Terms::find($menu->itemId);
        }
        ?>
        <tr>
            <td class="label"><?php echo e($item->name); ?>:</td>
            <td class="val">
                <strong><?php echo e($menu->count); ?></strong>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>




        <?php
        $extra_price = $booking->getJsonMeta('extra_price');
        ?>
        <?php if(!empty($extra_price) && count($extra_price) > 0): ?>

        <?php $__currentLoopData = $extra_price; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $extra_price): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
        $extra_price = json_encode($extra_price);
        $extra_price = json_decode($extra_price);
        ?>
        <tr>
            <td class="label"><?php echo e($extra_price->name); ?>:</td>
            <td class="val">
                <strong><?php echo e($extra_price->price); ?></strong>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>




        <?php
        $extra_price = $booking->getJsonMeta('extra_price');
        ?>
        <?php if(!empty($extra_price) && count($extra_price) > 0): ?>

        <?php $__currentLoopData = $extra_price; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $extra_price): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
        $extra_price = json_encode($extra_price);
        $extra_price = json_decode($extra_price);
        ?>
        <tr>
            <td class="label"><?php echo e($extra_price->name); ?>:</td>
            <td class="val">
                <strong><?php echo e($extra_price->price); ?></strong>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
        <?php if($booking->coupon_amount !== 0): ?>
        <tr>
            <td class="label fsz21"><?php echo e(__('Coupon')); ?></td>
            <td class="val fsz21"><strong style="color: #FA5636"><?php echo e(format_money($booking->coupon_amount)); ?></strong></td>
        </tr>
        <?php endif; ?>



        <tr>
            <td class="label fsz21"><?php echo e(__('Total')); ?></td>
            <td class="val fsz21"><strong style="color: #FA5636"><?php echo e(format_money($booking->total)); ?></strong></td>
        </tr>
        <tr>
            <td class="label fsz21"><?php echo e(__('Paid')); ?></td>
            <td class="val fsz21"><strong style="color: #FA5636"><?php echo e(format_money($booking->paid)); ?></strong></td>
        </tr>
        <?php if($booking->total > $booking->paid): ?>
        <tr>
            <td class="label fsz21"><?php echo e(__('Remain')); ?></td>
            <td class="val fsz21"><strong style="color: #FA5636"><?php echo e(format_money($booking->total - $booking->paid)); ?></strong></td>
        </tr>
        <?php endif; ?>
    </table>
</div>
<div class="text-center mt20">
    <a href="<?php echo e(route("user.booking_history")); ?>" target="_blank" class="btn btn-primary manage-booking-btn"><?php echo e(__('Manage Bookings')); ?></a>
</div><?php /**PATH C:\mamp\htdocs\Imkan\v1\modules/Tour/Views/emails/new_booking_detail.blade.php ENDPATH**/ ?>