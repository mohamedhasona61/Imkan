<div class="form-group">
    <label><?php echo e(__("Name")); ?></label>
    <input type="text" value="<?php echo e($translation->name); ?>" placeholder="<?php echo e(__("Extra name")); ?>" name="name" class="form-control">
</div>
<?php if(is_default_lang()): ?>

<div class="form-group">
    <label class="control-label"><?php echo e(__("Menu")); ?></label>
    <div class="">
        <select name="menu_id" class="form-control">
            <option value=""><?php echo e(__("-- Please Select --")); ?></option>
            <?php
            $menus = \Modules\Core\Models\MenuTour::whereNotNull('parent_id')->get();
            $oldMenuId = isset($row->menu_id) ? $row->menu_id : null;

            ?>

            <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($menu->id); ?>" <?php if($menu->id == $oldMenuId): ?> selected <?php endif; ?>><?php echo e($menu->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



        </select>
    </div>
</div>

<?php endif; ?>

<?php /**PATH C:\mamp\htdocs\flouka\Imkan\Imkan8-6\v1\modules/Tour/Views/admin/extras/form.blade.php ENDPATH**/ ?>