<div class="panel">
    <div class="panel-title"><strong><?php echo e(__("Tour Content")); ?></strong></div>
    <div class="panel-body">
        <div class="form-group">
            <label><?php echo e(__("Title")); ?></label>
            <input type="text" value="<?php echo clean($translation->title); ?>" placeholder="<?php echo e(__("Tour title")); ?>" name="title" class="form-control">
        </div>
        <div class="form-group">
            <label class="control-label"><?php echo e(__("Content")); ?></label>
            <div class="">
                <textarea name="content" class="d-none has-ckeditor" cols="30" rows="10"><?php echo e($translation->content); ?></textarea>
            </div>
        </div>
        <div class="form-group d-none">
            <label class="control-label"><?php echo e(__("Description")); ?></label>
            <div class="">
                <textarea name="short_desc" class="form-control" cols="30" rows="4"><?php echo e($translation->short_desc); ?></textarea>
            </div>
        </div>
        <?php if(is_default_lang()): ?>
        <div class="form-group">
            <label class="control-label"><?php echo e(__("Category")); ?></label>
            <div class="">
                <select name="category_id" class="form-control">
                    <option value=""><?php echo e(__("-- Please Select --")); ?></option>
                    <?php
                    $traverse = function ($categories, $prefix = '') use (&$traverse, $row) {
                        foreach ($categories as $category) {
                            $selected = '';
                            if ($row->category_id == $category->id)
                                $selected = 'selected';
                            printf("<option value='%s' %s>%s</option>", $category->id, $selected, $prefix . ' ' . $category->name);
                            $traverse($category->children, $prefix . '-');
                        }
                    };
                    $traverse($tour_category);
                    ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label"><?php echo e(__("Menus")); ?></label>
            <div class="">
                <select name="menu_id[]" class="form-control" multiple>
                    <option value=""><?php echo e(__("-- Please Select --")); ?></option>
                    <?php
                    $menus = \Modules\Core\Models\MenuTour::where('parent_id', null)->get();
                    $selectedMenus = json_decode($row->menu_id);
                    ?>
                        <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $selected = is_array($selectedMenus) && in_array($menu->id, $selectedMenus) ? 'selected' : '';
                        ?>
                        <option value="<?php echo e($menu->id); ?>" <?php echo e($selected); ?>><?php echo e($menu->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label"><?php echo e(__("Youtube Video")); ?></label>
            <input type="text" name="video" class="form-control" value="<?php echo e($row->video); ?>" placeholder="<?php echo e(__("Youtube link video")); ?>">
        </div>
        <?php if(is_default_lang()): ?>
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="control-label"><?php echo e(__("Tour Min People")); ?></label>
                    <input type="text" name="min_people" class="form-control" value="<?php echo e($row->min_people); ?>" placeholder="<?php echo e(__("Tour Min People")); ?>">
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="control-label"><?php echo e(__("Tour Max People")); ?></label>
                    <input type="text" name="max_people" class="form-control" value="<?php echo e($row->max_people); ?>" placeholder="<?php echo e(__("Tour Max People")); ?>">
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="control-label"><?php echo e(__("Duration")); ?></label>
                    <div class="input-group mb-3">
                        <input type="text" name="duration" class="form-control" value="<?php echo e($row->duration); ?>" placeholder="<?php echo e(__("Duration")); ?>" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <span class="input-group-text" id="basic-addon2"><?php echo e(__('hours')); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
        <?php do_action(\Modules\Tour\Hook::FORM_AFTER_MAX_PEOPLE, $row)?>
        <div class="form-group-item">
            <label class="control-label"><?php echo e(__('FAQs')); ?></label>
            <div class="g-items-header">
                <div class="row">
                    <div class="col-md-5"><?php echo e(__("Title")); ?></div>
                    <div class="col-md-5"><?php echo e(__('Content')); ?></div>
                    <div class="col-md-1"></div>
                </div>
            </div>
            <div class="g-items">
                <?php if(!empty($translation->faqs)): ?>
                <?php if(!is_array($translation->faqs)) $translation->faqs = json_decode($translation->faqs); ?>
                <?php $__currentLoopData = $translation->faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="item" data-number="<?php echo e($key); ?>">
                    <div class="row">
                        <div class="col-md-5">
                            <input type="text" name="faqs[<?php echo e($key); ?>][title]" class="form-control" value="<?php echo e($faq['title']); ?>" placeholder="<?php echo e(__('Eg: When and where does the tour end?')); ?>">
                        </div>
                        <div class="col-md-6">
                            <textarea name="faqs[<?php echo e($key); ?>][content]" class="form-control full-h" placeholder="..."><?php echo e($faq['content']); ?></textarea>
                        </div>
                        <div class="col-md-1">
                            <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
            <div class="text-right">
                <span class="btn btn-info btn-sm btn-add-item"><i class="icon ion-ios-add-circle-outline"></i> <?php echo e(__('Add item')); ?></span>
            </div>
            <div class="g-more hide">
                <div class="item" data-number="__number__">
                    <div class="row">
                        <div class="col-md-5">
                            <input type="text" __name__="faqs[__number__][title]" class="form-control" placeholder="<?php echo e(__('Eg: When and where does the tour end?')); ?>">
                        </div>
                        <div class="col-md-6">
                            <textarea __name__="faqs[__number__][content]" class="form-control full-h" placeholder="..."></textarea>
                        </div>
                        <div class="col-md-1">
                            <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if(is_default_lang()): ?>
        <div class="form-group">
            <label class="control-label"><?php echo e(__("Banner Image")); ?></label>
            <div class="form-group-image">
                <?php echo \Modules\Media\Helpers\FileHelper::fieldUpload('banner_image_id',$row->banner_image_id); ?>

            </div>
        </div>
        <div class="form-group">
            <label class="control-label"><?php echo e(__("Gallery")); ?></label>
            <?php echo \Modules\Media\Helpers\FileHelper::fieldGalleryUpload('gallery',$row->gallery); ?>

        </div>
        <?php endif; ?>
    </div>
</div><?php /**PATH /www/wwwroot/nileview.dokkan.design/v1/modules/Tour/Views/admin/tour/tour-content.blade.php ENDPATH**/ ?>