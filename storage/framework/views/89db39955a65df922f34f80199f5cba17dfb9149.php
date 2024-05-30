<div class="container">
    <div class="row">
        <div class="col-md-3 align-self-center">
            <?php if($title): ?>
                <div class="title">
                    <?php echo e($title); ?>

                </div>
            <?php endif; ?>
            <?php if(!empty($desc)): ?>
                <div class="sub-title">
                    <?php echo e($desc); ?>

                </div>
            <?php endif; ?>
        </div>
        <div class="col-md-9">
            <div class="list-item">
                <div class="owl-carousel">
                    <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $translation = $row->translate();
                        ?>
                        <div class="item-tour <?php echo e($wrap_class ?? ''); ?>">
                            <?php if($row->discount_percent): ?>
                                <div class="sale_info"><?php echo e($row->discount_percent); ?></div>
                            <?php endif; ?>
                            <?php if($row->is_featured == "1"): ?>
                                <div class="featured">
                                    <?php echo e(__("Featured")); ?>

                                </div>
                            <?php endif; ?>
                            <div class="thumb-image">
                                <a <?php if(!empty($blank)): ?> target="_blank" <?php endif; ?> href="<?php echo e($row->getDetailUrl($include_param ?? true)); ?>">
                                    <?php if($row->image_url): ?>
                                        <?php if(!empty($disable_lazyload)): ?>
                                            <img src="<?php echo e($row->image_url); ?>" class="img-responsive" alt="<?php echo e($location->name ?? ''); ?>">
                                        <?php else: ?>
                                            <?php echo get_image_tag($row->image_id,'medium',['class'=>'img-responsive','alt'=>$row->title]); ?>

                                        <?php endif; ?>
                                    <?php endif; ?>
                                </a>
                                <div class="service-wishlist <?php echo e($row->isWishList()); ?>" data-id="<?php echo e($row->id); ?>" data-type="<?php echo e($row->type); ?>">
                                    <i class="fa fa-heart"></i>
                                </div>
                            </div>
                            <div class="price">
                                <span class="onsale"><?php echo e($row->display_sale_price); ?></span>
                                <span class="text-price"> <span class="small">from</span> <?php echo e($row->display_price); ?></span>
                            </div>
                        </div>



                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /www/wwwroot/nileview.dokkan.design/v1/themes/BC/Tour/Views/frontend/blocks/list-tour/style-carousel-simple.blade.php ENDPATH**/ ?>