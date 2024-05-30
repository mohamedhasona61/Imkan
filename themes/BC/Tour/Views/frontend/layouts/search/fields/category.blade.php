@php
$location_search_style = setting_item('tour_location_search_style');
@endphp

<div class="form-group">
    <i class="field-icon fa icofont-map"></i>
    <div class="form-content">
        <label>{{ $field['title'] ?? '' }}</label>
        @if ($location_search_style == 'autocompletePlace')
            <div class="g-map-place">
                <input type="text" name="map_place" placeholder="{{ __('Choose Your Tour?') }}"
                    value="{{ request()->input('cat_id') }}" class="form-control border-0">
            </div>
        @else
            @php
                $category_name = '';
                $list_json = [];
                $traverse = function ($categories, $prefix = '') use (&$traverse, &$list_json, &$category_name) {
                    foreach ($categories as $category) {
                        $translate = $category->translate();
                        if (Request::query('cat_id') == $category->id) {
                            $category_name = $translate->name;
                        }

                        $list_json[] = [
                            'id' => $category->id,
                            'title' => $prefix . ' ' . $translate->name,
                        ];
                        $traverse($category->children, $prefix . '-');
                    }
                };

                $categories = Modules\Tour\Models\TourCategory::get();
                $traverse($categories);
            @endphp
            <div class="smart-search">
                <input type="text" class="smart-search-location parent_text form-control"
                    {{ empty(setting_item('tour_location_search_style')) || setting_item('tour_location_search_style') == 'normal' ? 'readonly' : '' }}
                    placeholder="{{ __('Choose Your Tour Type?') }}" value="{{ $category_name }}"
                    data-onLoad="{{ __('Loading...') }}" data-default="{{ json_encode($list_json) }}">
                <input type="hidden" class="child_id" name="cat_id" value="{{ Request::query('cat_id') }}">
            </div>
        @endif
    </div>
</div>
