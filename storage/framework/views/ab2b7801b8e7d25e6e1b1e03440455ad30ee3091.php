<form action="<?php echo e(route('tour.search')); ?>" class="form bravo_form p-3" method="get">
    <div class="g-field-search">
        <div class="row">
            <div class=" col-md-12 text-right">
                
            </div>
            <?php
                $tour_search_fields = setting_item_array('tour_search_fields');
                $tour_search_fields = array_values(\Illuminate\Support\Arr::sort($tour_search_fields, function ($value) {
                    return $value['position'] ?? 0;
                }));
            ?>
            <?php if(!empty($tour_search_fields)): ?>
                <?php $__currentLoopData = $tour_search_fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $field['title'] = $field['title_'.app()->getLocale()] ?? $field['title'] ?? "" ?>
                    <div class="col-md-12">
                        <?php switch($field['field']):
                            case ('service_name'): ?>
                                <?php echo $__env->make('Tour::frontend.layouts.search.fields.service_name', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <?php break; ?>
                            <?php case ('location'): ?>
                                <?php echo $__env->make('Tour::frontend.layouts.search.fields.location', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <?php break; ?>
                            <?php case ('category'): ?>
                                <?php echo $__env->make('Tour::frontend.layouts.search.fields.category', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <?php break; ?>
                            <?php case ('date'): ?>
                                <?php echo $__env->make('Tour::frontend.layouts.search.fields.date', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <?php break; ?>
                            <?php case ('attr'): ?>
                                <?php echo $__env->make('Tour::frontend.layouts.search.fields.attr', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <?php break; ?>
                        <?php endswitch; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
            <div class=" col-md-12 mt-3">
                <button class="btn btn-primary btn-search btn-tour-search-design w-100 " style="border-radius: 5px; height: 35px;" type="submit"><?php echo e(__("Search")); ?></button>
            </div>
        </div>
    </div>

</form>
<?php /**PATH /www/wwwroot/nileview.dokkan.design/v1/themes/BC/Tour/Views/frontend/layouts/search/form-search.blade.php ENDPATH**/ ?>