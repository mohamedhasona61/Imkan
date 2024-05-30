

<?php $__env->startSection('content'); ?>
    <form action="<?php echo e(route('hotel.admin.room.store',['hotel_id'=>$hotel->id,'id'=>($row->id) ? $row->id : '-1','lang'=>request()->query('lang')])); ?>" method="post">
        <?php echo csrf_field(); ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-9">
                    <div class="d-flex justify-content-between mb20">
                        <div class="">
                            <h1 class="title-bar"><?php echo e($row->id ? __('Edit: ').$row->title : __('Add new Hotel Room')); ?></h1>
                        </div>
                    </div>
                    <?php echo $__env->make('admin.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php if($row->id): ?>
                        <?php echo $__env->make('Language::admin.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php endif; ?>
                    <div class="lang-content-box">
                        <div class="panel">
                            <div class="panel-title"><strong><?php echo e(__("Room information")); ?></strong></div>
                            <div class="panel-body">
                                <?php echo $__env->make('Hotel::admin.room.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>
                            <div class="panel-footer">
                                <button class="btn btn-success" type="submit"><i class="fa fa-save"></i> <?php echo e(__("Save Changes")); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\mamp\htdocs\flouka\v1\modules/Hotel/Views/admin/room/detail.blade.php ENDPATH**/ ?>