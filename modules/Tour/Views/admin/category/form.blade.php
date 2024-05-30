<div class="form-group">
    <label>{{__("Name")}}</label>
    <input type="text" value="{{$translation->name}}" placeholder="{{__("Category name")}}" name="name" class="form-control">
</div>
<div class="form-group">
    <label>{{__("Content")}}</label>
    <textarea placeholder="{{__("Category Description")}}" name="content" class="form-control">{{$translation->content}}</textarea>
</div>


<div class="form-group">
    <label class="control-label">{{__("Youtube Video")}}</label>
    <input type="text" name="video" class="form-control" value="{{$row->video}}" placeholder="{{__("Youtube link video")}}">
</div>


<div class="panel">
    <div class="panel-title"><strong>{{__("Availability")}}</strong></div>
    <div class="panel-body">
        <h3 class="panel-body-title">{{__('Open Hours')}}</h3>
        <div class="form-group">
            <label>
                <input type="checkbox" name="enable_open_hours" @if(!empty($row->meta->enable_open_hours)) checked @endif value="1"> {{__('Enable Open Hours')}}
            </label>
        </div>
        <?php $old = $row->meta->open_hours ?? []; ?>
        <div class="table-responsive form-group" data-condition="enable_open_hours:is(1)">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{__('Enable?')}}</th>
                        <th>{{__('Open')}}</th>
                        <th>{{__('Close')}}</th>
                    </tr>
                </thead>
                @for($i = 1 ; $i <=1 ; $i++) <tr>
                    <td>
                        <input style="display: inline-block" type="checkbox" @if($old[$i]['enable'] ?? false ) checked @endif name="open_hours[{{$i}}][enable]" value="1">
                    </td>
                    <td>
                        <select class="form-control" name="start_from">
                            <?php
                            $time = strtotime('2023-01-01 00:00:00');
                            for ($k = 0; $k <= 23; $k++) :
                                $val = date('H:i', $time + 60 * 60 * $k);
                            ?>
                                <option @if(isset($old[$i]) && $old[$i]['from']==$val) selected @endif value="{{$val}}">{{$val}}</option>
                            <?php endfor; ?>
                        </select>
                    </td>
                    <td>
                        <select class="form-control" name="end_at">
                            <?php
                            $time = strtotime('2023-01-01 00:00:00');
                            for ($k = 0; $k <= 23; $k++) :
                                $val = date('H:i', $time + 60 * 60 * $k);
                            ?>
                                <option @if(isset($old[$i]) && $old[$i]['to']==$val) selected @endif value="{{$val}}">{{$val}}</option>
                            <?php endfor; ?>
                        </select>
                    </td>
                    </tr>
                    @endfor
            </table>
        </div>
    </div>
</div>




<div class="form-group">
    <label class="control-label">{{__("Banner Image")}}</label>
    <div class="form-group-image">
        {!! \Modules\Media\Helpers\FileHelper::fieldUpload('banner_image_id',$row->banner_image_id) !!}
    </div>
</div>


@if(is_default_lang())
    <div class="form-group">
        <label>{{__("Parent")}}</label>
        <select name="parent_id" class="form-control">
            <option value="">{{__("-- Please Select --")}}</option>
            <?php
            $traverse = function ($categories, $prefix = '') use (&$traverse, $row) {
                foreach ($categories as $category) {
                    if ($category->id == $row->id) {
                        continue;
                    }
                    $selected = '';
                    if ($row->parent_id == $category->id)
                        $selected = 'selected';
                    printf("<option value='%s' %s>%s</option>", $category->id, $selected, $prefix . ' ' . $category->name);
                    $traverse($category->children, $prefix . '-');
                }
            };
            $traverse($parents);
            ?>
        </select>
    </div>
    @php do_action(\Modules\Tour\Hook::FORM_AFTER_CATEGORY,$row) @endphp
@endif
{{--<div class="form-group">--}}
    {{--<label class="control-label">{{__("Description")}}</label>--}}
    {{--<div class="">--}}
        {{--<textarea name="content" class="d-none has-ckeditor" cols="30" rows="10">{{$translation->content}}</textarea>--}}
    {{--</div>--}}
{{--</div>--}}
