
<style>
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="number"] {
        -moz-appearance: textfield;
    }
</style>
<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('vendor.update_booking')); ?>">
    <?php echo csrf_field(); ?>
    <div class="container-fluid">
        <?php echo $__env->make('admin.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="lang-content-box">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel">
                        <div class="panel-title"><strong><?php echo e(__('Edit Booking')); ?></strong></div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-4">
                                    <input type="hidden" value="<?php echo e($row->id); ?>" name="booking_id" class="form-control">
                                    <input type="hidden" value="<?php echo e($row->object_id); ?>" name="tour_id" class="form-control">

                                    <div class="form-group">
                                        <label><?php echo e(__('First Name')); ?></label>
                                        <input type="text" value="<?php echo e($row->first_name); ?>" placeholder="<?php echo e(__('First Name')); ?>" name="first_name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label><?php echo e(__('Last Name')); ?></label>
                                        <input type="text" value="<?php echo e($row->last_name); ?>" placeholder="<?php echo e(__('Last Name')); ?>" name="last_name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label><?php echo e(__('Email')); ?></label>
                                        <input type="text" value="<?php echo e($row->email); ?>" placeholder="<?php echo e(__('Email')); ?>" name="email" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label><?php echo e(__('Phone')); ?></label>
                                        <input type="text" value="<?php echo e($row->phone); ?>" placeholder="<?php echo e(__('Phone')); ?>" name="phone" class="form-control">
                                    </div>
                                </div>
                                
                     
                                
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label><?php echo e(__('Total Guests')); ?></label>
                                        <input type="number" value="<?php echo e($row->total_guests); ?>" placeholder="<?php echo e(__('Total Guests')); ?>" min="1" max="<?php echo e($row->service->max_people); ?>" name="total_guests" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label><?php echo e(__('Extra Price')); ?></label>
                                        <input type="number"  value="<?php echo e($row->extra_price); ?>" placeholder="<?php echo e(__('Extra Price')); ?>" min="0"  name="extra_price" class="form-control">
                                    </div>
                                </div>
                                
                                                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label><?php echo e(__('Total Price')); ?></label>
                                        <input type="number" readonly value="<?php echo e($row->total); ?>" placeholder="<?php echo e(__('Total Price')); ?>" min="1" max="<?php echo e($row->total); ?>" name="total" class="form-control">
                                    </div>
                                </div>

                                
                                           
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label><?php echo e(__('Tour Start At')); ?></label>
                                        <input type="time" value="<?php echo e($booking_dates[0]['start_from']); ?>" readonly  placeholder="<?php echo e(__('Tour Start At')); ?>"  class="form-control">
                                    </div>
                                </div>
                                        
                                <?php if($booking_dates->count()==1): ?>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label><?php echo e(__('Tour End At')); ?></label>
                                        <input type="time" value="<?php echo e($booking_dates[0]['end_at']); ?>" readonly placeholder="<?php echo e(__('Tour End At')); ?>"  class="form-control">
                                    </div>
                                </div>
                                <?php else: ?> 
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label><?php echo e(__('Tour End At')); ?></label>
                                        <input type="time" value="<?php echo e($booking_dates[1]['end_at']); ?>" readonly placeholder="<?php echo e(__('Tour End At')); ?>"  class="form-control">
                                    </div>
                                </div>
                                <?php endif; ?>

                                
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label><?php echo e(__('Custom Requirement')); ?></label>
                                        <textarea type="text"  value="<?php echo e($row->custom_requirement); ?>"  placeholder="<?php echo e(__('Custom Requirement')); ?>"  name="custom_requirement" class="form-control"> <?php echo e($row->custom_requirement); ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <?php
                                $choosed_extra_price_array = json_decode($choosed_extra_price, true);
                                ?>

                                <?php if($extra_price != null): ?>

                                <?php $__currentLoopData = $extra_price; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $price): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <input type="checkbox" name="extra_price[]" class="form-control" value="<?php echo e($price['name']); ?>" placeholder="<?php echo e(__('Extra Price')); ?>" <?php if(in_array($price['name'], array_column($choosed_extra_price_array, 'name' ))): ?> checked <?php endif; ?>>
                                        <label class="control-label"><?php echo e($price['name']); ?> (<?php echo e($price['price']); ?>) </label>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <?php endif; ?>
                            </div>

                                            <?php if(!empty($menusWithChildren)): ?>                           
                            
                               <div class="row">
                                <h2><?php echo e(__('Menus')); ?></h2>
                            </div>
                            <?php endif; ?>


                            <?php $__currentLoopData = $menusWithChildren; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $main_menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="row mb-4" style="border-bottom: 0.5px solid rgba(128, 128, 128, 0.348)">
                                <div class="col-12 pt-2 pb-2 " style="background-color: #263a53 ; color: white">
                                    <h5><?php echo e($main_menu->name); ?></h5>
                                </div>
                                <?php $__currentLoopData = $main_menu->menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inside_menus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="row w-100 m-0 p-0 mt-2">
                                    <div class="col-12 pt-1 pb-1" style="background-color: #eaeef3 ; color: rgb(0, 0, 0)">
                                        <h6><?php echo e($inside_menus->name); ?></h6>
                                    </div>
                                    <?php $__currentLoopData = $inside_menus->terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <div class="text-center">
                                                <label class="control-label"><?php echo e($term->name); ?> (<?php echo e($term->price); ?>)</label>
                                            </div>
                                            <div class="d-flex justify-content-around align-items center">
                                                <button data-mainMenu-id="<?php echo e($main_menu['id']); ?>" data-insideMenu-id="<?php echo e($inside_menus['id']); ?>" data-term-id="<?php echo e($term['id']); ?>" id="<?php echo e('increase-' . $main_menu['id'] . '-' . $inside_menus['id'] . '-' . $term['id']); ?>" class=" btn btn-sm increase" style="background-color: unset ; border:  0.5px solid #263a53">
                                                    +</button>
                                                <?php
                                                $selected_item_count = 0;
                                                
                                            
                                                ?>
                                                
                                                <?php $__currentLoopData = $choosed_menus_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $single_choosed_menus_array): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                if ($single_choosed_menus_array['submenuId'] == $single_choosed_menus_array['itemId']) {
                                                    if ($single_choosed_menus_array['submenuId'] == $inside_menus['id'] && $single_choosed_menus_array['menuId'] == $main_menu['id']) {
                                                        $selected_item_count = $single_choosed_menus_array['count'];
                                                    }
                                                } else {
                                                    if ($single_choosed_menus_array['itemId'] == $term['id'] && $single_choosed_menus_array['submenuId'] == $inside_menus['id'] && $single_choosed_menus_array['menuId'] == $main_menu['id']) {
                                                        $selected_item_count = $single_choosed_menus_array['count'];
                                                    }
                                                }
                                                ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                <input data-mainMenu-id="<?php echo e($main_menu['id']); ?>" data-insideMenu-id="<?php echo e($inside_menus['id']); ?>" data-term-id="<?php echo e($term['id']); ?>" id="<?php echo e('input-' . $main_menu['id'] . '-' . $inside_menus['id'] . '-' . $term['id']); ?>" style="background-color: unset ; border:  0.5px solid #263a53 ; width: 50px;" type="number" value="<?php echo e($selected_item_count); ?>" class="form-control text-center" placeholder="<?php echo e(__('Extra Price')); ?>">
                                                <button data-mainMenu-id="<?php echo e($main_menu['id']); ?>" data-insideMenu-id="<?php echo e($inside_menus['id']); ?>" data-term-id="<?php echo e($term['id']); ?>" id="<?php echo e('decrease-' . $main_menu['id'] . '-' . $inside_menus['id'] . '-' . $term['id']); ?>" class="btn btn-sm decrease" style="background-color: unset ; border: 0.5px solid #263a53">
                                                    -
                                                </button>
                                                <input id="selected-menus-input" type="hidden" name="selected-menus-input[]" value="">

                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="panel">
                        <div class="panel-title"><strong><?php echo e(__('Update Booking')); ?></strong></div>
                        <div class="panel-body">
                            <div class="text-right">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i>
                                    <?php echo e(__('Save Changes')); ?></button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
</form>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('js'); ?>
<script>
    $(document).ready(() => {
        let choosed_menus_array = <?php echo json_encode($choosed_menus_array, 15, 512) ?>;
        const updateArrayAndFields = () => {
            $('#selected-menus-input').val(JSON.stringify(choosed_menus_array));
            choosed_menus_array.forEach(item => {
                const {
                    menuId,
                    submenuId,
                    itemId,
                    count
                } = item;
                const inputId = `#input-${menuId}-${submenuId}-${itemId}`;
                $(inputId).val(count);
            });
        };
        $('.increase').on('click', function() {
            const mainMenuId = $(this).data('mainmenu-id');
            const insideMenuId = $(this).data('insidemenu-id');
            const termId = $(this).data('term-id');
            const existingItem = choosed_menus_array.find(item => item.submenuId === insideMenuId && item.itemId === termId);
            if (existingItem) {
                existingItem.count++;
            } else {
                choosed_menus_array.push({
                    menuId: mainMenuId,
                    submenuId: insideMenuId,
                    itemId: termId,
                    count: 1
                });
            }
            updateArrayAndFields();
        });
        $('.decrease').on('click', function() {
            const mainMenuId = $(this).data('mainmenu-id');
            const insideMenuId = $(this).data('insidemenu-id');
            const termId = $(this).data('term-id');
            const existingItem = choosed_menus_array.find(item => item.submenuId === insideMenuId && item.itemId === termId);
            if (existingItem) {
                if (existingItem.count > 1) {
                    existingItem.count--;
                } else {
                    const existingItemIndex = choosed_menus_array.indexOf(existingItem);
                    if (existingItemIndex !== -1) {
                        choosed_menus_array.splice(existingItemIndex, 1);
                    }
                }
            }
            updateArrayAndFields();
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/nileview.dokkan.design/v1/themes/Base/User/Views/frontend/EditBooking.blade.php ENDPATH**/ ?>