@extends('admin.layouts.app')
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
@section('content')
<form method="POST" action="{{ route('vendor.update_booking') }}">
    @csrf
    <div class="container-fluid">
        @include('admin.message')
        <div class="lang-content-box">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel">
                        <div class="panel-title"><strong>{{ __('Edit Booking') }}</strong></div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-4">
                                    <input type="hidden" value="{{ $row->id }}" name="booking_id" class="form-control">
                                    <input type="hidden" value="{{ $row->object_id }}" name="tour_id" class="form-control">

                                    <div class="form-group">
                                        <label>{{ __('First Name') }}</label>
                                        <input type="text" value="{{ $row->first_name }}" placeholder="{{ __('First Name') }}" name="first_name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>{{ __('Last Name') }}</label>
                                        <input type="text" value="{{ $row->last_name }}" placeholder="{{ __('Last Name') }}" name="last_name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>{{ __('Email') }}</label>
                                        <input type="text" value="{{ $row->email }}" placeholder="{{ __('Email') }}" name="email" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>{{ __('Phone') }}</label>
                                        <input type="text" value="{{ $row->phone }}" placeholder="{{ __('Phone') }}" name="phone" class="form-control">
                                    </div>
                                </div>
                                
                     
                                
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>{{ __('Total Guests') }}</label>
                                        <input type="number" value="{{ $row->total_guests }}" placeholder="{{ __('Total Guests') }}" min="1" max="{{ $row->service->max_people }}" name="total_guests" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>{{ __('Extra Price') }}</label>
                                        <input type="number"  value="{{ $row->extra_price }}" placeholder="{{ __('Extra Price') }}" min="0"  name="extra_price" class="form-control">
                                    </div>
                                </div>
                                
                                                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>{{ __('Total Price') }}</label>
                                        <input type="number" readonly value="{{ $row->total }}" placeholder="{{ __('Total Price') }}" min="1" max="{{ $row->total }}" name="total" class="form-control">
                                    </div>
                                </div>

                                
                                           
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>{{ __('Tour Start At') }}</label>
                                        <input type="time" value="{{ $booking_dates[0]['start_from'] }}" readonly  placeholder="{{ __('Tour Start At') }}"  class="form-control">
                                    </div>
                                </div>
                                        
                                @if($booking_dates->count()==1)
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>{{ __('Tour End At') }}</label>
                                        <input type="time" value="{{ $booking_dates[0]['end_at'] }}" readonly placeholder="{{ __('Tour End At') }}"  class="form-control">
                                    </div>
                                </div>
                                @else 
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>{{ __('Tour End At') }}</label>
                                        <input type="time" value="{{ $booking_dates[1]['end_at'] }}" readonly placeholder="{{ __('Tour End At') }}"  class="form-control">
                                    </div>
                                </div>
                                @endif

                                
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>{{ __('Custom Requirement') }}</label>
                                        <textarea type="text"  value="{{ $row->custom_requirement }}"  placeholder="{{ __('Custom Requirement') }}"  name="custom_requirement" class="form-control"> {{ $row->custom_requirement }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                @php
                                $choosed_extra_price_array = json_decode($choosed_extra_price, true);
                                @endphp

                                @if($extra_price != null)

                                @foreach ($extra_price as $price)
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <input type="checkbox" name="extra_price[]" class="form-control" value="{{ $price['name'] }}" placeholder="{{ __('Extra Price') }}" @if (in_array($price['name'], array_column($choosed_extra_price_array, 'name' ))) checked @endif>
                                        <label class="control-label">{{ $price['name'] }} ({{$price['price']}}) </label>
                                    </div>
                                </div>
                                @endforeach

                                @endif
                            </div>

                                            @if (!empty($menusWithChildren))                           
                            
                               <div class="row">
                                <h2>{{ __('Menus') }}</h2>
                            </div>
                            @endif


                            @foreach ($menusWithChildren as $main_menu)
                            <div class="row mb-4" style="border-bottom: 0.5px solid rgba(128, 128, 128, 0.348)">
                                <div class="col-12 pt-2 pb-2 " style="background-color: #263a53 ; color: white">
                                    <h5>{{ $main_menu->name }}</h5>
                                </div>
                                @foreach ($main_menu->menus as $inside_menus)
                                <div class="row w-100 m-0 p-0 mt-2">
                                    <div class="col-12 pt-1 pb-1" style="background-color: #eaeef3 ; color: rgb(0, 0, 0)">
                                        <h6>{{ $inside_menus->name }}</h6>
                                    </div>
                                    @foreach ($inside_menus->terms as $term)
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <div class="text-center">
                                                <label class="control-label">{{ $term->name }} ({{$term->price}})</label>
                                            </div>
                                            <div class="d-flex justify-content-around align-items center">
                                                <button data-mainMenu-id="{{ $main_menu['id'] }}" data-insideMenu-id="{{ $inside_menus['id'] }}" data-term-id="{{ $term['id'] }}" id="{{ 'increase-' . $main_menu['id'] . '-' . $inside_menus['id'] . '-' . $term['id'] }}" class=" btn btn-sm increase" style="background-color: unset ; border:  0.5px solid #263a53">
                                                    +</button>
                                                <?php
                                                $selected_item_count = 0;
                                                
                                            
                                                ?>
                                                
                                                @foreach ($choosed_menus_array as $single_choosed_menus_array)
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
                                                @endforeach

                                                <input data-mainMenu-id="{{ $main_menu['id'] }}" data-insideMenu-id="{{ $inside_menus['id'] }}" data-term-id="{{ $term['id'] }}" id="{{ 'input-' . $main_menu['id'] . '-' . $inside_menus['id'] . '-' . $term['id'] }}" style="background-color: unset ; border:  0.5px solid #263a53 ; width: 50px;" type="number" value="{{ $selected_item_count }}" class="form-control text-center" placeholder="{{ __('Extra Price') }}">
                                                <button data-mainMenu-id="{{ $main_menu['id'] }}" data-insideMenu-id="{{ $inside_menus['id'] }}" data-term-id="{{ $term['id'] }}" id="{{ 'decrease-' . $main_menu['id'] . '-' . $inside_menus['id'] . '-' . $term['id'] }}" class="btn btn-sm decrease" style="background-color: unset ; border: 0.5px solid #263a53">
                                                    -
                                                </button>
                                                <input id="selected-menus-input" type="hidden" name="selected-menus-input[]" value="">

                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endforeach
                            </div>
                            @endforeach

                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="panel">
                        <div class="panel-title"><strong>{{ __('Update Booking') }}</strong></div>
                        <div class="panel-body">
                            <div class="text-right">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i>
                                    {{ __('Save Changes') }}</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
</form>
{{-- <input type="text" hidden id="choosed" value="{{ $choosed_menus_array }}"> --}}
@endsection
@push('js')
<script>
    $(document).ready(() => {
        let choosed_menus_array = @json($choosed_menus_array);
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
@endpush