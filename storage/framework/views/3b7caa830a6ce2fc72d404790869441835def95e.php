<div class="mb-3">
    <label class="d-block" for="exampleInputEmail1"><?php echo e(__("Location")); ?></label>
    <?php
    $location = !empty(Request()->location_id) ? \Modules\Location\Models\Location::find(Request()->location_id) : false;
    \App\Helpers\AdminForm::select2('location_id', [
        'configs' => [
            'ajax'        => [
                'url'      => route('location.admin.getForSelect2'),
                'dataType' => 'json',
            ],
            'allowClear'  => true,
            'placeholder' => __('-- All Location --')
        ]
    ], !empty($location->id) ? [
        $location->id,
        $location->name
    ] : false)
    ?>
</div>
<?php /**PATH C:\mamp\htdocs\Imkan\v1\modules/Core/Views/admin/global/advanced-filter.blade.php ENDPATH**/ ?>