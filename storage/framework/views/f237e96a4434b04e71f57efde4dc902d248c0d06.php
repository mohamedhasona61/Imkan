<?php
    $translation = $row->translate();
?>
<div class="item-tour <?php echo e($wrap_class ?? ''); ?> row ">
    <?php if($row->is_featured == '1'): ?>
        <div class="featured">
            <?php echo e(__('Featured')); ?>

        </div>
    <?php endif; ?>
    <div class="thumb-image col-5 " style="height:unset;padding: 0px;">
        <?php if($row->discount_percent): ?>
            <div class="sale_info"><?php echo e($row->discount_percent); ?></div>
        <?php endif; ?>
        <a <?php if(!empty($blank)): ?> target="_blank" <?php endif; ?>
            href="<?php echo e($row->getDetailUrl($include_param ?? true)); ?>">
            <?php if($row->image_url): ?>
                <?php if(!empty($disable_lazyload)): ?>
                    <img style="object-fit: contain !important;width: 100%; height: unset;" src="<?php echo e($row->image_url); ?>" class="img-responsive" alt="<?php echo e($location->name ?? ''); ?>">
                <?php else: ?>
                    <?php echo get_image_tag($row->image_id, 'medium', ['class' => 'img-responsive', 'alt' => $row->title]); ?>

                <?php endif; ?>
            <?php endif; ?>
        </a>
        <div class="service-wishlist <?php echo e($row->isWishList()); ?>" data-id="<?php echo e($row->id); ?>"
            data-type="<?php echo e($row->type); ?>">
            <i class="fa fa-heart"></i>
        </div>
    </div>
    <div class="col-6">
        <div class="location">
            <?php if(!empty($row->location->name)): ?>
                <?php $location =  $row->location->translate() ?>
                <i class="icofont-paper-plane"></i>
                <?php echo e($location->name ?? ''); ?>

            <?php endif; ?>
        </div>
        <div class="item-title">
            <a <?php if(!empty($blank)): ?> target="_blank" <?php endif; ?>
                href="<?php echo e($row->getDetailUrl($include_param ?? true)); ?>">
                <?php echo e($translation->title); ?>

            </a>
        </div>
        
        
    </div>
</div>
<?php /**PATH /www/wwwroot/nileview.dokkan.design/v1/themes/BC/Tour/Views/frontend/layouts/search/loop-grid.blade.php ENDPATH**/ ?>