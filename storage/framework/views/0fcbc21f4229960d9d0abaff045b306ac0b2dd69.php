<tr>
    <td class="booking-history-type">
        <?php if($service = $booking->service): ?>
        <i class="<?php echo e($service->getServiceIconFeatured()); ?>"></i>
        <?php endif; ?>
        <small><?php echo e($booking->object_model); ?></small>
    </td>
    <td>
        <?php if($service = $booking->service): ?>
        <?php
        $translation = $service->translate();
        ?>
        <a target="_blank" href="<?php echo e($service->getDetailUrl()); ?>">
            <?php echo e($translation->title); ?>

        </a>
        <?php else: ?>
        <?php echo e(__("[Deleted]")); ?>

        <?php endif; ?>
    </td>
    <td>
    <?php
    $category_id = $service->category_id ?? 1;
    $category = \Modules\Tour\Models\TourCategory::find($category_id);
    $translate = $category ? $category->translate() : null;
        $url = $category ? $category->getDetailUrl() : '#';

    ?>
    <a target="_blank" href="<?php echo e($url); ?>">
        <?php if($translate): ?>
            <?php echo e($translate->name); ?>

        <?php else: ?>
            <?php echo e(__("[Deleted]")); ?>

        <?php endif; ?>
    </a>
</td>


<?php 


$time=\Modules\Tour\Models\Bookning_dates::where('booking_id',$booking->id)->get();


if($time->count() ==1)
{

$start_at = $time[0]['start_from'];
$end_at = $time[0]['end_at'];
}else{
$start_at = $time[0]['start_from'];
$end_at = $time[1]['end_at'];
}




?>
     

    <td class="a-hidden"><?php echo e(display_date($booking->created_at)); ?></td>
    <td class="a-hidden">
        <?php echo e(__("Booking Day")); ?> : <?php echo e(display_date($booking->start_date)); ?> <br>
        
        <?php echo e(__("Start At")); ?> :<?php echo e($start_at); ?><br>
        <?php echo e(__("End At")); ?> :<?php echo e($end_at); ?>

        
    </td>
    <td><?php echo e(format_money($booking->total)); ?></td>
    <td><?php echo e(format_money($booking->paid)); ?></td>
    <td><?php echo e(format_money($booking->total - $booking->paid)); ?></td>
    <td class="<?php echo e($booking->status); ?> a-hidden">
        <?php echo e($booking->statusName); ?>

    </td>
    <td width="2%">
                        <?php if($service = $booking->service): ?>
        <a class="btn btn-xs btn-primary btn-info-booking" data-ajax="<?php echo e(route('booking.modal',['booking'=>$booking])); ?>" data-toggle="modal" data-id="<?php echo e($booking->id); ?>" data-target="#modal_booking_detail">
            <i class="fa fa-info-circle"></i><?php echo e(__("Details")); ?>

        </a>
        <?php endif; ?>

        <a href="<?php echo e(route('user.booking.invoice',['code'=>$booking->code])); ?>" class="btn btn-xs btn-primary btn-info-booking open-new-window mt-1" onclick="window.open(this.href); return false;">
            <i class="fa fa-print"></i><?php echo e(__("Invoice")); ?>

        </a>
        <?php if($booking->status == 'unpaid'): ?>
        <a href="<?php echo e(route('booking.checkout',['code'=>$booking->code])); ?>" class="btn btn-xs btn-primary btn-info-booking open-new-window mt-1">
            <?php echo e(__("Pay now")); ?>

        </a>
        <?php endif; ?>
        
        <?php if($booking->customer_id == auth()->user()->id ||$booking->service->author_id == auth()->user()->id): ?> 
        <?php if($booking->status !== 'completed' && $booking->status !== 'cancelled'): ?>
        <button class="btn btn-xs btn-primary btn-info-booking open-new-window mt-1" onclick="openCancelBookingModal('<?php echo e(route('user.cancel_booking', ['id' => $booking->id])); ?>')">
            <i class="fa fa-times"></i><?php echo e(__("Cancel")); ?>

        </button>
        <?php endif; ?>
        <?php endif; ?>

        <?php if($booking->vendor_id == auth()->user()->id): ?>
        <?php if($booking->status == 'processing'): ?>
        <button class="btn btn-xs btn-primary btn-info-booking open-new-window mt-1" onclick="redirectTo('<?php echo e(route('vendor.edit_booking', ['id' => $booking->id])); ?>')">
            <i class="fa fa-times"></i><?php echo e(__("Update Booking")); ?>

        </button>
        <?php endif; ?>
        <?php endif; ?>

        <div id="message"></div>

    </td>


</tr>

<!-- Modal -->
<div class="modal fade" id="cancelBookingModal" tabindex="-1" aria-labelledby="cancelBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelBookingModalLabel"><?php echo e(__("Cancel Booking")); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><?php echo e(__("Are you sure you want to cancel this booking?")); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(__("Close")); ?></button>
                <button type="button" class="btn btn-danger" id="confirmCancelBooking"><?php echo e(__("Confirm")); ?></button>
                <button type="button" style="display: none;" class="btn btn-danger" id="confirmCancelBookingWithRefunded"><?php echo e(__("Confirm")); ?></button>
            </div>
        </div>
    </div>
</div><?php /**PATH /www/wwwroot/nileview.dokkan.design/v1/themes/Base/Tour/Views/frontend/bookingHistory/loop.blade.php ENDPATH**/ ?>