<div class="form-group">
    <label>{{__("Name")}}</label>
    <input type="text" value="{{$translation->name}}" placeholder="{{__("Menu name")}}" name="name" class="form-control">
</div>


<div class="form-group">
    <label>{{__("Description")}}</label>
    <textarea placeholder="{{__("Menu Description")}}" name="description" class="form-control">{{$translation->description}}</textarea>
</div>

@if(is_default_lang())
<div class="form-group">
    <label>{{__("Price")}}</label>
    <input type="text" value="{{$row->price}}" placeholder="{{__("Menu Price")}}" name="price" class="form-control">
</div>
<div class="form-group">
    <label>{{__("Extra Count")}}</label>
    <input type="text" value="{{$row->extra_count}}" placeholder="{{__("Extra Count")}}" name="extra_count" class="form-control">
</div>
<div class="form-group">
    <label class="control-label">{{__("Categories")}}</label>
    <div class="">
        <select name="category_id" class="form-control">
            <option value="">{{__("-- Please Select --")}}</option>
            @php
            $categories = \Modules\Tour\Models\TourCategory::get();
            @endphp
            @foreach($categories as $category)
            <option value="{{$category->id}}" {{$category->id == $row->category_id ? 'selected' : ''}}>
                {{$category->name}}
            </option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label class="control-label">{{__("Parent")}}</label>
    <div class="">
        <select name="parent_id" class="form-control">
            <option value="">{{__("-- Please Select --")}}</option>
            @php
            $menus = \Modules\Core\Models\MenuTour::where('parent_id' , null)->get();
            @endphp

            @foreach($menus as $menu)
            <option value="{{$menu->id}}"{{$menu->id == $row->parent_id ? 'selected' : ''}}>{{$menu->name}}</option>
            @endforeach

        </select>
    </div>
</div>
<div class="form-group">
    <input type="checkbox" name="check_maximum" value="1" class="form-control" id="check_maximum" onchange="updateCheckboxValue(this)">
    <label>{{__("Minimum and Maximum")}}</label>
</div>
<div class="panel d-none">
    <div class="panel-title"><strong>{{__("Pricing")}}</strong></div>
    <div class="panel-body">
  
        @if(is_default_lang())
            <h3 class="panel-body-title">{{__('Person Types')}}</h3>
            <div class="form-group">
                <label><input type="checkbox" name="enable_person_types" @if(!empty($row->meta->enable_person_types)) checked @endif value="1"> {{__('Enable Person Types')}}
                </label>
            </div>
            <div class="form-group-item" data-condition="enable_person_types:is(1)">
                <label class="control-label">{{__('Person Types')}}</label>
                <div class="g-items-header">
                    <div class="row">
                        <div class="col-md-5">{{__("Person Type")}}</div>
                        <div class="col-md-2">{{__('Min')}}</div>
                        <div class="col-md-2">{{__('Price')}}</div>
                        <div class="col-md-2">{{__('Special Price')}}</div>
                        <div class="col-md-1"></div>
                    </div>
                </div>
                <div class="g-items">
                    <?php  $languages = \Modules\Language\Models\Language::getActive();  ?>
                    @if(!empty($row->meta->person_types))
                        @foreach($row->meta->person_types as $key=>$person_type)
                            <div class="item" data-number="{{$key}}">
                                <div class="row">
                                    <div class="col-md-5">
                                        @if(!empty($languages) && setting_item('site_enable_multi_lang') && setting_item('site_locale'))
                                            @foreach($languages as $language)
                                                <?php $key_lang = setting_item('site_locale') != $language->locale ? "_".$language->locale : ""   ?>
                                                <div class="g-lang">
                                                    <div class="title-lang">{{$language->name}}</div>
                                                    <input type="text" name="person_types[{{$key}}][name{{$key_lang}}]" class="form-control" value="{{$person_type['name'.$key_lang] ?? ''}}" placeholder="{{__('Eg: Adults')}}">
                                                    <input type="text" name="person_types[{{$key}}][desc{{$key_lang}}]" class="form-control" value="{{$person_type['desc'.$key_lang] ?? ''}}" placeholder="{{__('Description')}}">
                                                </div>
                                            @endforeach
                                        @else
                                            <input type="text" name="person_types[{{$key}}][name]" class="form-control" value="{{$person_type['name'] ?? ''}}" placeholder="{{__('Eg: Adults')}}">
                                            <input type="text" name="person_types[{{$key}}][desc]" class="form-control" value="{{$person_type['desc'] ?? ''}}" placeholder="{{__('Description')}}">
                                        @endif
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" min="0" name="person_types[{{$key}}][min]" class="form-control" value="{{$person_type['min'] ?? 0}}" placeholder="{{__("Minimum per booking")}}">
                                    </div>
                            
                                    <div class="col-md-2">
                                        <input type="text" min="0" name="person_types[{{$key}}][price]" class="form-control" value="{{$person_type['price'] ?? 0}}">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" min="0" name="person_types[{{$key}}][special_price]" class="form-control" value="{{$person_type['special_price'] ?? 0}}">
                                    </div>


                                    <div class="col-md-1">
                                        <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="text-right">
                    <span class="btn btn-info btn-sm btn-add-item"><i class="icon ion-ios-add-circle-outline"></i> {{__('Add item')}}</span>
                </div>
                <div class="g-more hide">
                    <div class="item" data-number="__number__">
                        <div class="row">
                            <div class="col-md-5">
                                @if(!empty($languages) && setting_item('site_enable_multi_lang') && setting_item('site_locale'))
                                    @foreach($languages as $language)
                                        <?php $key = setting_item('site_locale') != $language->locale ? "_".$language->locale : ""   ?>
                                        <div class="g-lang">
                                            <div class="title-lang">{{$language->name}}</div>
                                            <input type="text" __name__="person_types[__number__][name{{$key}}]" class="form-control" value="" placeholder="{{__('Eg: Adults')}}">
                                            <input type="text" __name__="person_types[__number__][desc{{$key}}]" class="form-control" value="" placeholder="{{__('Description')}}">
                                        </div>
                                    @endforeach
                                @else
                                    <input type="text" __name__="person_types[__number__][name]" class="form-control" value="" placeholder="{{__('Eg: Adults')}}">
                                    <input type="text" __name__="person_types[__number__][desc]" class="form-control" value="" placeholder="{{__('Description')}}">
                                @endif
                            </div>
                            <div class="col-md-2">
                                <input type="number" min="0" __name__="person_types[__number__][min]" class="form-control" value="" placeholder="{{__("Minimum per booking")}}">
                            </div>
             
                            <div class="col-md-2">
                                <input type="text" min="0" __name__="person_types[__number__][price]" class="form-control" value="" placeholder="{{__("per 1 item")}}">
                            </div>
                            <div class="col-md-2">
                                <input type="text" min="0" __name__="person_types[__number__][special_price]" class="form-control" value="" placeholder="{{__("per 1 item")}}">
                            </div>

                            <div class="col-md-1">
                                <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif


    </div>
</div>

@endif