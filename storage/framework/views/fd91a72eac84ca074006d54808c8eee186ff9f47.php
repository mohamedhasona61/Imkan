<?php $__env->startPush('css'); ?>
    <link href="<?php echo e(asset('dist/frontend/module/tour/css/tour.css?_ver=' . config('app.asset_version'))); ?>" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('libs/ion_rangeslider/css/ion.rangeSlider.min.css')); ?>" />
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
    <div class="bravo_search_tour">
        <div class="bravo_banner" style="padding: 0px !important;background-color: white !important;">
            <?php
                $bg = setting_item('tour_page_search_banner');
                $bgids = explode(',', $bg);
            ?>
            <div class="bravo-form-search-slider d-none d-lg-block">
                <div class="effect">
                    <div class="owl-carousel" dir="ltr">
                        <?php $__currentLoopData = $bgids; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($id != ''): ?>
                                <div class="item"> <img src="<?php echo e(get_file_url($id, 'full')); ?>" alt=""> </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
            <?php
                $bgMobile = setting_item('tour_page_search_banner_mobile');
                $bgmobileids = explode(',', $bgMobile);
            ?>
            <div class="bravo-form-search-slider d-block d-lg-none ">
                <div class="effect">
                    <div class="owl-carousel" dir="ltr">
                        <?php $__currentLoopData = $bgmobileids; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($id != ''): ?>
                                <div class="item"> <img src="<?php echo e(get_file_url($id, 'full')); ?>" alt=""> </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="bravo_form_search">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <?php echo $__env->make('Tour::frontend.layouts.search.form-search', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> 
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <?php echo $__env->make('Tour::frontend.layouts.search.list-item', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('js'); ?>
    <script type="text/javascript" src="<?php echo e(asset('libs/ion_rangeslider/js/ion.rangeSlider.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('module/tour/js/tour.js?_ver=' . config('app.asset_version'))); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/nileview.dokkan.design/v1/themes/BC/Tour/Views/frontend/search.blade.php ENDPATH**/ ?>