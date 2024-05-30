<form action="{{ route('tour.search') }}" class="form bravo_form p-3" method="get">
    <div class="g-field-search">
        <div class="row">
            <div class=" col-md-12 text-right">
                
            </div>
            @php
                $tour_search_fields = setting_item_array('tour_search_fields');
                $tour_search_fields = array_values(\Illuminate\Support\Arr::sort($tour_search_fields, function ($value) {
                    return $value['position'] ?? 0;
                }));
            @endphp
            @if(!empty($tour_search_fields))
                @foreach($tour_search_fields as $field)
                    @php $field['title'] = $field['title_'.app()->getLocale()] ?? $field['title'] ?? "" @endphp
                    <div class="col-md-12">
                        @switch($field['field'])
                            @case('service_name')
                                @include('Tour::frontend.layouts.search.fields.service_name')
                                @break
                            @case('location')
                                @include('Tour::frontend.layouts.search.fields.location')
                                @break
                            @case('category')
                                @include('Tour::frontend.layouts.search.fields.category')
                                @break
                            @case('date')
                                @include('Tour::frontend.layouts.search.fields.date')
                                @break
                            @case('attr')
                                @include('Tour::frontend.layouts.search.fields.attr')
                                @break
                        @endswitch
                    </div>
                @endforeach
            @endif
            <div class=" col-md-12 mt-3">
                <button class="btn btn-primary btn-search btn-tour-search-design w-100 " style="border-radius: 5px; height: 35px;" type="submit">{{ __("Search") }}</button>
            </div>
        </div>
    </div>

</form>
