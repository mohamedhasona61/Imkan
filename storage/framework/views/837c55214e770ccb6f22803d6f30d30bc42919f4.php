
<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar"><?php echo e(__("Menu Extras")); ?></h1>
        </div>
        <?php echo $__env->make('admin.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="row">
            <div class="col-md-4 mb40">
                <div class="panel">
                    <div class="panel-title"><?php echo e(__("Add Extra")); ?></div>
                    <div class="panel-body">
                        <form action="<?php echo e(route('tour.admin.extras.store',['id'=>($row->id) ? $row->id : '-1','lang'=>request()->query('lang')])); ?>" method="post">
                            <?php echo csrf_field(); ?>
                            <?php echo $__env->make('Tour::admin/extras/form',['parents'=>$rows], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
                       
                    </div>
                    <div class="col-left">
                        <form method="get" action="<?php echo e(route('tour.admin.extras.index')); ?> " class="filter-form filter-form-right d-flex justify-content-end" role="search">
                            <input type="text" name="s" value="<?php echo e(Request()->s); ?>" class="form-control" placeholder="<?php echo e(__("Search by name")); ?>">
                            <button class="btn-info btn btn-icon btn_search" id="search-submit" type="submit"><?php echo e(__('Search')); ?></button>
                        </form>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-title"><?php echo e(__("All Extras")); ?></div>
                    <div class="panel-body">
                        <form class="bravo-form-item">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th><?php echo e(__("Name")); ?></th>
                                    <th class=""><?php echo e(__("Actions")); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(count($rows) > 0): ?>
                                    <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td class="title">
                                                <a href="<?php echo e(route('tour.admin.extras.edit', ['id' => $row->id])); ?>"><?php echo e($row->name); ?></a>
                                            </td>
                                   
                                            <td>
                                                <a href="<?php echo e(route('tour.admin.extras.edit', ['id' => $row->id])); ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> <?php echo e(__('Edit')); ?>

                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3"><?php echo e(__("No data")); ?></td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\mamp\htdocs\Imkan\v1\modules/Tour/Views/admin/extras/index.blade.php ENDPATH**/ ?>