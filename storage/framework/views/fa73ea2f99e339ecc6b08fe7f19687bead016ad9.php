<?php ($location_search_style = setting_item('tour_location_search_style')); ?>
<div class="form-group">
	<i class="field-icon fa icofont-map"></i>
	<div class="form-content">
		<label><?php echo e($field['title'] ?? ""); ?></label>
		<?php if($location_search_style=='autocompletePlace'): ?>
			<div class="g-map-place" >
				<input type="text" name="map_place" placeholder="<?php echo e(__("Choose Your Tour?")); ?>"  value="<?php echo e(request()->input('cat_id')); ?>" class="form-control border-0">
			</div>
		<?php else: ?>
<?php
$category_name = '';
$list_json = [];

$traverse = function ($categories, $prefix = '') use (&$traverse, &$list_json, &$category_name) {
    foreach ($categories as $category) {
        $translate = $category->translate();
        if (Request::query('cat_id') == $category->id) {
            $category_name = $translate->name;
        }
        $list_json[] = [
            'id'    => $category->id,
            'title' => $prefix.' '.$translate->name,
        ];
        $traverse($category->children, $prefix.'-');
    }
};

$categories = Modules\Tour\Models\TourCategory::get();

$traverse($categories);
?>

<div class="smart-search">
    <input type="text" class="smart-search-location parent_text form-control" <?php echo e(( empty(setting_item("tour_location_search_style")) or setting_item("tour_location_search_style") == "normal" ) ? "readonly" : ""); ?> placeholder="<?php echo e(__("Choose Your Tour?")); ?>" value="<?php echo e($category_name); ?>" data-onLoad="<?php echo e(__("Loading...")); ?>" data-default="<?php echo e(json_encode($list_json)); ?>">
<input type="hidden" class="child_id" name="cat_id" value="<?php echo e(Request::query('cat_id')); ?>">
</div>
<?php endif; ?><?php /**PATH C:\mamp\htdocs\flouka\Imkan\Imkan8-6\v1\themes/BC/Tour/Views/frontend/layouts/search/fields/category.blade.php ENDPATH**/ ?>