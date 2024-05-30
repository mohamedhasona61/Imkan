
<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar"><?php echo e(__("Attribute: :name",['name'=>$menu->name])); ?></h1>
        </div>
        <?php echo $__env->make('admin.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="row">
            <div class="col-md-4 mb40">
                <div class="panel">
                    <div class="panel-title"><?php echo e(__("Add Term")); ?></div>
                    <div class="panel-body">
                        <form action="<?php echo e(route('tour.admin.menu.term.store',['id'=>($row->id) ? $row->id : '-1','lang'=>request()->query('lang')])); ?>" method="post">
                            <?php echo csrf_field(); ?>
                            <?php echo $__env->make('Tour::admin/terms_menu/form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <div class="">
                                <button class="btn btn-primary" type="submit"><?php echo e(__("Add new")); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="filter-div d-flex justify-content-between ">
                    <div class="col-left">
                        <?php if(!empty($rows)): ?>
                            <form method="post" action="<?php echo e(route('tour.admin.menu.term.editTermBulk')); ?>" class="filter-form filter-form-left d-flex justify-content-start">
                                <?php echo e(csrf_field()); ?>

                                <select name="action" class="form-control">
                                    <option value=""><?php echo e(__(" Bulk Action ")); ?></option>
                                    <option value="delete"><?php echo e(__(" Delete ")); ?></option>
                                </select>
                                <button data-confirm="<?php echo e(__("Do you want to delete?")); ?>" class="btn-info btn btn-icon dungdt-apply-form-btn" type="button"><?php echo e(__('Apply')); ?></button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <div class="col-left">
                        <form method="get" action="<?php echo e(route('tour.admin.menu.term.index',['menu_id' => $menu->id])); ?> " class="filter-form filter-form-right d-flex justify-content-end" role="search">
                            <input type="text" name="s" value="<?php echo e(Request()->s); ?>" class="form-control" placeholder="<?php echo e(__("Search by name")); ?>">
                            <button class="btn-info btn btn-icon btn_search" id="search-submit" type="submit"><?php echo e(__('Search')); ?></button>
                        </form>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-title"><?php echo e(__("All Terms")); ?></div>
                    <div class="panel-body">
                        <form class="bravo-form-item">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th width="60px"><input type="checkbox" class="check-all"></th>
                                    <th><?php echo e(__("Name")); ?></th>
                                    <th class="date"><?php echo e(__("Date")); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(count($rows) > 0): ?>
                                    <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><input type="checkbox" class="check-item" name="ids[]" value="<?php echo e($row->id); ?>"></td>
                                            <td class="title">
                                                <a href="<?php echo e(route('tour.admin.menu.term.edit', ['id' => $row->id])); ?>"><?php echo e($row->name); ?></a>
                                            </td>
                                            <td><?php echo e(display_date($row->updated_at)); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4"><?php echo e(__("No data")); ?></td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                                <?php echo e($rows->appends(request()->query())->links()); ?>

                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/nileview.dokkan.design/v1/modules/Tour/Views/admin/terms_menu/index.blade.php ENDPATH**/ ?>