<div class="form-group">
    <label>{{__("Name")}}</label>
    <input type="text" value="{{$translation->name}}" placeholder="{{__("Extra name")}}" name="name" class="form-control">
</div>
@if(is_default_lang())

<div class="form-group">
    <label class="control-label">{{__("Menu")}}</label>
    <div class="">
        <select name="menu_id" class="form-control">
            <option value="">{{__("-- Please Select --")}}</option>
            @php
            $menus = \Modules\Core\Models\MenuTour::whereNotNull('parent_id')->get();
            $oldMenuId = isset($row->menu_id) ? $row->menu_id : null;

            @endphp

            @foreach($menus as $menu)
            <option value="{{$menu->id}}" @if($menu->id == $oldMenuId) selected @endif>{{$menu->name}}</option>
            @endforeach



        </select>
    </div>
</div>

@endif

