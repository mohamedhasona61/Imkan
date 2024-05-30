<?php $__env->startSection('content'); ?>
<h2 class="title-bar no-border-bottom">
    <?php echo e(__("Booking History")); ?>

</h2>
<?php echo $__env->make('admin.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div class="booking-history-manager">
    <div class="tabbable">
        <ul class="nav nav-tabs ht-nav-tabs">
            <?php $status_type = Request::query('status'); ?>
            <li class="<?php if(empty($status_type)): ?> active <?php endif; ?>">
                <a href="<?php echo e(route("user.booking_history")); ?>"><?php echo e(__("All Booking")); ?></a>
            </li>
            <?php if(!empty($statues)): ?>
            <?php $__currentLoopData = $statues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="<?php if(!empty($status_type) && $status_type == $status): ?> active <?php endif; ?>">
                <a href="<?php echo e(route("user.booking_history",['status'=>$status])); ?>"><?php echo e(booking_status_to_text($status)); ?></a>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </ul>
        <?php if(auth()->user()->role_id ==1 || auth()->user()->role_id ==2): ?>
       <form action="<?php echo e(route('user.filter.booking_history')); ?>" method="Post">
                <?php echo csrf_field(); ?>
            <div class="row">
                <div class="col-md-6">
                    
                <div class="form-group mr-2">
                    <label for="date_filter"><?php echo e(__('Filter by Date:')); ?></label>
                    <input type="date" name="date_filter" id="date_filter" class="form-control" value="<?php echo e(Request::query('date_filter')); ?>">
                </div>
            </div>
                <div class="col-md-6">

                
                <div class="form-group">
                    <label for="category_id"><?php echo e(__('Filter by Category:')); ?></label>
                    <select name="category_id" id="category_id" class="form-control">
                        <option value=""><?php echo e(__('All Categories')); ?></option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($category->id); ?>" <?php if(Request::query('category_filter')==$category->id): ?> selected <?php endif; ?>>
                            <?php echo e($category->name); ?> (<?php echo e($category->id); ?>)
                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            </div>
            
            <div class="row">
                <div class="col-md-6"><button type="submit" class="btn btn-primary"><?php echo e(__('Apply Filters')); ?></button></div>
            </div>
            </form>
        <?php endif; ?>
        <?php if(!empty($bookings) and $bookings->total() > 0): ?>
        <div class="tab-content">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-booking-history">
                    <thead>
                        <tr>
                            <th width="2%"><?php echo e(__("Type")); ?></th>
                            <th><?php echo e(__("Title")); ?></th>
                            <th><?php echo e(__("Category")); ?></th>
                            <th class="a-hidden"><?php echo e(__("Order Date")); ?></th>
                            <th class="a-hidden"><?php echo e(__("Execution Time")); ?></th>
                            <th><?php echo e(__("Total")); ?></th>
                            <th><?php echo e(__("Paid")); ?></th>
                            <th><?php echo e(__("Remain")); ?></th>
                            <th class="a-hidden"><?php echo e(__("Status")); ?></th>
                            <th><?php echo e(__("Action")); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        ?> 
                        <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo $__env->make(ucfirst($booking->object_model).'::frontend.bookingHistory.loop', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="bravo-pagination">
                <?php echo e($bookings->appends(request()->query())->links()); ?>

            </div>
        </div>
        <?php else: ?>
        <?php echo e(__("No Booking History")); ?>

        <?php endif; ?>
    </div>
    <div class="modal" tabindex="-1" id="modal_booking_detail">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo e(__('Booking ID: #')); ?> <span class="user_id"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-center"><?php echo e(__("Loading...")); ?></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(__('Close')); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('js'); ?>
<script>
    $('#modal_booking_detail').on('show.bs.modal', function(e) {
        var btn = $(e.relatedTarget);
        $(this).find('.user_id').html(btn.data('id'));
        $(this).find('.modal-body').html('<div class="d-flex justify-content-center"><?php echo e(__("Loading...")); ?></div>');
        var modal = $(this);
        $.get(btn.data('ajax'), function(html) {
            modal.find('.modal-body').html(html);
        })
    })



    function redirectTo(url) {
        window.location.href = url;
    }
</script>

<script>
    function openCancelBookingModal(cancelUrl) {
        document.getElementById('confirmCancelBooking').setAttribute('onclick', `cancelBooking('${cancelUrl}')`);
        $('#cancelBookingModal').modal('show');
    }

    function cancelBooking(cancelUrl) {
        $.ajax({
            url: cancelUrl,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#cancelBookingModal .modal-body').html('<p>Booking cancelled successfully!</p>');
                    setTimeout(function() {
                        $('#cancelBookingModal').modal('hide');
                        location.reload();

                    }, 4000);
                } else {
                    $('#cancelBookingModal .modal-body').html('<p>' + response.message + '</p>');
                    $('#confirmCancelBooking').css('display', 'none');
                    $('#confirmCancelBookingWithRefunded').css('display', 'block');

                    $('#confirmCancelBookingWithRefunded').on('click', function() {
                        $.ajax({
                            url: cancelUrl,
                            type: 'GET',
                            data: {
                                refunded: 1
                            },
                            success: function(newResponse) {
                                $('#cancelBookingModal .modal-body').html('<p>Booking cancelled successfully!</p>');
                                setTimeout(function() {
                                    $('#cancelBookingModal').modal('hide');
                                    location.reload();
                                }, 4000);
                            },
                            error: function(newError) {
                                $('#cancelBookingModal').modal('hide');
                            }
                        });
                    });
                }
            },
            error: function(error) {
                $('#cancelBookingModal').modal('hide');

            }
        });
    }
</script>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.user', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/nileview.dokkan.design/v1/themes/Base/User/Views/frontend/bookingHistory.blade.php ENDPATH**/ ?>