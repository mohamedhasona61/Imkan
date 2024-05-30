<div class="effect">
    <div class="owl-carousel">
        <?php $__currentLoopData = $list_slider; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $img = get_file_url($item['bg_image'],'full') ?>
            <div class="item" style="background-image: linear-gradient(0deg,rgba(0, 0, 0, 0.2),rgba(0, 0, 0, 0.2)),url('<?php echo e($img); ?>') !important">
                <h2 class="sub-heading text-center"><?php echo e($item['desc'] ?? ""); ?></h2>
                <h1 class="text-heading text-center"><?php echo e($item['title'] ?? ""); ?></h1>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <?php if(empty($hide_form_search)): ?>
                <div class="g-form-control">
                    <ul class="nav nav-tabs" role="tablist">
                        <?php if(!empty($service_types)): ?>
                            <?php $number = 0; ?>
                            <?php $__currentLoopData = $service_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $allServices = get_bookable_services();
                                    if(empty($allServices[$service_type])) continue;
                                    $module = new $allServices[$service_type];
                                ?>
                                <li role="bravo_<?php echo e($service_type); ?>">
                                    <a href="#bravo_<?php echo e($service_type); ?>" class="<?php if($number == 0): ?> active <?php endif; ?>" aria-controls="bravo_<?php echo e($service_type); ?>" role="tab" data-toggle="tab">
                                        <i class="<?php echo e($module->getServiceIconFeatured()); ?>"></i>
                                        <?php echo e(!empty($modelBlock["title_for_".$service_type]) ? $modelBlock["title_for_".$service_type] : $module->getModelName()); ?>

                                    </a>
                                </li>
                                <?php $number++; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </ul>
                    <div class="tab-content">
                        <?php if(!empty($service_types)): ?>
                            <?php $number = 0; ?>
                            <?php $__currentLoopData = $service_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $allServices = get_bookable_services();
                                    if(empty($allServices[$service_type])) continue;
                                    $module = new $allServices[$service_type];
                                ?>
                                <div role="tabpanel" class="tab-pane <?php if($number == 0): ?> active <?php endif; ?>" id="bravo_<?php echo e($service_type); ?>">
                                    <?php echo $__env->make(ucfirst($service_type).'::frontend.layouts.search.form-search', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                                <?php $number++; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div><?php /**PATH /www/wwwroot/nileview.dokkan.design/v1/themes/Base/Template/Views/frontend/blocks/form-search-all-service/style-slider-ver2.blade.php ENDPATH**/ ?>