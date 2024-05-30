<div class="form-group">
    <i class="field-icon fa icofont-search"></i>
    <div class="form-content">
        <label><?php echo e($field['title'] ?? ""); ?></label>
        <div class="input-search">
            <input type="text" name="service_name" class="form-control" placeholder="<?php echo e(__("Search for...")); ?>" value="<?php echo e(request()->input("service_name")); ?>">
        </div>
    </div>
</div><?php /**PATH /www/wwwroot/nileview.dokkan.design/v1/themes/BC/Tour/Views/frontend/layouts/search/fields/service_name.blade.php ENDPATH**/ ?>