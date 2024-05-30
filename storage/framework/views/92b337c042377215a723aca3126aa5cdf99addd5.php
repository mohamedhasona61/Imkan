<div class="form-group">
    <label><?php echo e(__("Name")); ?></label>
    <input type="text" value="<?php echo e($translation->name); ?>" placeholder="<?php echo e(__("Menu name")); ?>" name="name" class="form-control">
</div>


<div class="form-group">
    <label><?php echo e(__("Description")); ?></label>
    <textarea placeholder="<?php echo e(__("Menu Description")); ?>" name="description" class="form-control"><?php echo e($translation->description); ?></textarea>
</div>

<?php if(is_default_lang()): ?>
<div class="form-group">
    <label><?php echo e(__("Price")); ?></label>
    <input type="text" value="<?php echo e($row->price); ?>" placeholder="<?php echo e(__("Menu Price")); ?>" name="price" class="form-control">
</div>
<div class="form-group">
    <label><?php echo e(__("Extra Count")); ?></label>
    <input type="text" value="<?php echo e($row->extra_count); ?>" placeholder="<?php echo e(__("Extra Count")); ?>" name="extra_count" class="form-control">
</div>
<div class="form-group">
    <label class="control-label"><?php echo e(__("Categories")); ?></label>
    <div class="">
        <select name="category_id" class="form-control">
            <option value=""><?php echo e(__("-- Please Select --")); ?></option>
            <?php
            $categories = \Modules\Tour\Models\TourCategory::get();
            ?>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($category->id); ?>" <?php echo e($category->id == $row->category_id ? 'selected' : ''); ?>>
                <?php echo e($category->name); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
</div>
<div class="form-group">
    <label class="control-label"><?php echo e(__("Parent")); ?></label>
    <div class="">
        <select name="parent_id" class="form-control">
            <option value=""><?php echo e(__("-- Please Select --")); ?></option>
            <?php
            $menus = \Modules\Core\Models\MenuTour::where('parent_id' , null)->get();
            ?>

            <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($menu->id); ?>"<?php echo e($menu->id == $row->parent_id ? 'selected' : ''); ?>><?php echo e($menu->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </select>
    </div>
</div>
<div class="form-group">
    <input type="checkbox" name="check_maximum" value="1" class="form-control" id="check_maximum" onchange="updateCheckboxValue(this)">
    <label><?php echo e(__("Minimum and Maximum")); ?></label>
</div>
<div class="panel d-none">
    <div class="panel-title"><strong><?php echo e(__("Pricing")); ?></strong></div>
    <div class="panel-body">
  
        <?php if(is_default_lang()): ?>
            <h3 class="panel-body-title"><?php echo e(__('Person Types')); ?></h3>
            <div class="form-group">
                <label><input type="checkbox" name="enable_person_types" <?php if(!empty($row->meta->enable_person_types)): ?> checked <?php endif; ?> value="1"> <?php echo e(__('Enable Person Types')); ?>

                </label>
            </div>
            <div class="form-group-item" data-condition="enable_person_types:is(1)">
                <label class="control-label"><?php echo e(__('Person Types')); ?></label>
                <div class="g-items-header">
                    <div class="row">
                        <div class="col-md-5"><?php echo e(__("Person Type")); ?></div>
                        <div class="col-md-2"><?php echo e(__('Min')); ?></div>
                        <div class="col-md-2"><?php echo e(__('Price')); ?></div>
                        <div class="col-md-2"><?php echo e(__('Special Price')); ?></div>
                        <div class="col-md-1"></div>
                    </div>
                </div>
                <div class="g-items">
                    <?php  $languages = \Modules\Language\Models\Language::getActive();  ?>
                    <?php if(!empty($row->meta->person_types)): ?>
                        <?php $__currentLoopData = $row->meta->person_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$person_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="item" data-number="<?php echo e($key); ?>">
                                <div class="row">
                                    <div class="col-md-5">
                                        <?php if(!empty($languages) && setting_item('site_enable_multi_lang') && setting_item('site_locale')): ?>
                                            <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php $key_lang = setting_item('site_locale') != $language->locale ? "_".$language->locale : ""   ?>
                                                <div class="g-lang">
                                                    <div class="title-lang"><?php echo e($language->name); ?></div>
                                                    <input type="text" name="person_types[<?php echo e($key); ?>][name<?php echo e($key_lang); ?>]" class="form-control" value="<?php echo e($person_type['name'.$key_lang] ?? ''); ?>" placeholder="<?php echo e(__('Eg: Adults')); ?>">
                                                    <input type="text" name="person_types[<?php echo e($key); ?>][desc<?php echo e($key_lang); ?>]" class="form-control" value="<?php echo e($person_type['desc'.$key_lang] ?? ''); ?>" placeholder="<?php echo e(__('Description')); ?>">
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <input type="text" name="person_types[<?php echo e($key); ?>][name]" class="form-control" value="<?php echo e($person_type['name'] ?? ''); ?>" placeholder="<?php echo e(__('Eg: Adults')); ?>">
                                            <input type="text" name="person_types[<?php echo e($key); ?>][desc]" class="form-control" value="<?php echo e($person_type['desc'] ?? ''); ?>" placeholder="<?php echo e(__('Description')); ?>">
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" min="0" name="person_types[<?php echo e($key); ?>][min]" class="form-control" value="<?php echo e($person_type['min'] ?? 0); ?>" placeholder="<?php echo e(__("Minimum per booking")); ?>">
                                    </div>
                            
                                    <div class="col-md-2">
                                        <input type="text" min="0" name="person_types[<?php echo e($key); ?>][price]" class="form-control" value="<?php echo e($person_type['price'] ?? 0); ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" min="0" name="person_types[<?php echo e($key); ?>][special_price]" class="form-control" value="<?php echo e($person_type['special_price'] ?? 0); ?>">
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
                                <?php if(!empty($languages) && setting_item('site_enable_multi_lang') && setting_item('site_locale')): ?>
                                    <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $key = setting_item('site_locale') != $language->locale ? "_".$language->locale : ""   ?>
                                        <div class="g-lang">
                                            <div class="title-lang"><?php echo e($language->name); ?></div>
                                            <input type="text" __name__="person_types[__number__][name<?php echo e($key); ?>]" class="form-control" value="" placeholder="<?php echo e(__('Eg: Adults')); ?>">
                                            <input type="text" __name__="person_types[__number__][desc<?php echo e($key); ?>]" class="form-control" value="" placeholder="<?php echo e(__('Description')); ?>">
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <input type="text" __name__="person_types[__number__][name]" class="form-control" value="" placeholder="<?php echo e(__('Eg: Adults')); ?>">
                                    <input type="text" __name__="person_types[__number__][desc]" class="form-control" value="" placeholder="<?php echo e(__('Description')); ?>">
                                <?php endif; ?>
                            </div>
                            <div class="col-md-2">
                                <input type="number" min="0" __name__="person_types[__number__][min]" class="form-control" value="" placeholder="<?php echo e(__("Minimum per booking")); ?>">
                            </div>
             
                            <div class="col-md-2">
                                <input type="text" min="0" __name__="person_types[__number__][price]" class="form-control" value="" placeholder="<?php echo e(__("per 1 item")); ?>">
                            </div>
                            <div class="col-md-2">
                                <input type="text" min="0" __name__="person_types[__number__][special_price]" class="form-control" value="" placeholder="<?php echo e(__("per 1 item")); ?>">
                            </div>

                            <div class="col-md-1">
                                <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>


    </div>
</div>

<?php endif; ?><?php /**PATH C:\mamp\htdocs\Imkan\v1\modules/Tour/Views/admin/menu/form.blade.php ENDPATH**/ ?>