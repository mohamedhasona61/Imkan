<div class="form-group">
    <label><?php echo e(__("Name")); ?></label>
    <input type="text" value="<?php echo e($translation->name); ?>" placeholder="<?php echo e(__("Category name")); ?>" name="name" class="form-control">
</div>
<div class="form-group">
    <label><?php echo e(__("Content")); ?></label>
    <textarea placeholder="<?php echo e(__("Category Description")); ?>" name="content" class="form-control"><?php echo e($translation->content); ?></textarea>
</div>


<div class="form-group">
    <label class="control-label"><?php echo e(__("Youtube Video")); ?></label>
    <input type="text" name="video" class="form-control" value="<?php echo e($row->video); ?>" placeholder="<?php echo e(__("Youtube link video")); ?>">
</div>


<div class="panel">
    <div class="panel-title"><strong><?php echo e(__("Availability")); ?></strong></div>
    <div class="panel-body">
        <h3 class="panel-body-title"><?php echo e(__('Open Hours')); ?></h3>
        <div class="form-group">
            <label>
                <input type="checkbox" name="enable_open_hours" <?php if(!empty($row->meta->enable_open_hours)): ?> checked <?php endif; ?> value="1"> <?php echo e(__('Enable Open Hours')); ?>

            </label>
        </div>
        <?php $old = $row->meta->open_hours ?? []; ?>
        <div class="table-responsive form-group" data-condition="enable_open_hours:is(1)">
            <table class="table">
                <thead>
                    <tr>
                        <th><?php echo e(__('Enable?')); ?></th>
                        <th><?php echo e(__('Open')); ?></th>
                        <th><?php echo e(__('Close')); ?></th>
                    </tr>
                </thead>
                <?php for($i = 1 ; $i <=1 ; $i++): ?> <tr>
                    <td>
                        <input style="display: inline-block" type="checkbox" <?php if($old[$i]['enable'] ?? false ): ?> checked <?php endif; ?> name="open_hours[<?php echo e($i); ?>][enable]" value="1">
                    </td>
                    <td>
                        <select class="form-control" name="start_from">
                            <?php
                            $time = strtotime('2023-01-01 00:00:00');
                            for ($k = 0; $k <= 23; $k++) :
                                $val = date('H:i', $time + 60 * 60 * $k);
                            ?>
                                <option <?php if(isset($old[$i]) && $old[$i]['from']==$val): ?> selected <?php endif; ?> value="<?php echo e($val); ?>"><?php echo e($val); ?></option>
                            <?php endfor; ?>
                        </select>
                    </td>
                    <td>
                        <select class="form-control" name="end_at">
                            <?php
                            $time = strtotime('2023-01-01 00:00:00');
                            for ($k = 0; $k <= 23; $k++) :
                                $val = date('H:i', $time + 60 * 60 * $k);
                            ?>
                                <option <?php if(isset($old[$i]) && $old[$i]['to']==$val): ?> selected <?php endif; ?> value="<?php echo e($val); ?>"><?php echo e($val); ?></option>
                            <?php endfor; ?>
                        </select>
                    </td>
                    </tr>
                    <?php endfor; ?>
            </table>
        </div>
    </div>
</div>




<div class="form-group">
    <label class="control-label"><?php echo e(__("Banner Image")); ?></label>
    <div class="form-group-image">
        <?php echo \Modules\Media\Helpers\FileHelper::fieldUpload('banner_image_id',$row->banner_image_id); ?>

    </div>
</div>


<?php if(is_default_lang()): ?>
    <div class="form-group">
        <label><?php echo e(__("Parent")); ?></label>
        <select name="parent_id" class="form-control">
            <option value=""><?php echo e(__("-- Please Select --")); ?></option>
            <?php
            $traverse = function ($categories, $prefix = '') use (&$traverse, $row) {
                foreach ($categories as $category) {
                    if ($category->id == $row->id) {
                        continue;
                    }
                    $selected = '';
                    if ($row->parent_id == $category->id)
                        $selected = 'selected';
                    printf("<option value='%s' %s>%s</option>", $category->id, $selected, $prefix . ' ' . $category->name);
                    $traverse($category->children, $prefix . '-');
                }
            };
            $traverse($parents);
            ?>
        </select>
    </div>
    <?php do_action(\Modules\Tour\Hook::FORM_AFTER_CATEGORY,$row) ?>
<?php endif; ?>

    
    
        
    

<?php /**PATH /www/wwwroot/nileview.dokkan.design/v1/modules/Tour/Views/admin/category/form.blade.php ENDPATH**/ ?>