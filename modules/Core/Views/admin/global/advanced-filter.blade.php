<div class="mb-3">
    <label class="d-block" for="exampleInputEmail1">{{ __("Location") }}</label>
    @php
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
    @endphp
</div>
