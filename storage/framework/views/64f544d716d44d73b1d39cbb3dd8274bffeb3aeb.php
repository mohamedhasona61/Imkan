<?php if($row->banner_image_id): ?>
    <div class="bravo_banner" style="background-image: linear-gradient(0deg,rgba(0, 0, 0, 0),rgba(0, 0, 0, 0.2)),url('<?php echo e($row->getBannerImageUrlAttribute('full')); ?>') !important"></div>
<?php endif; ?>

<?php /**PATH /www/wwwroot/nileview.dokkan.design/v1/themes/BC/Location/Views/frontend/layouts/details/location-banner.blade.php ENDPATH**/ ?>