


<div class="panel">
    <div class="panel-title"><strong>{{__("Availability")}}</strong></div>
    <div class="panel-body">
        <h3 class="panel-body-title">{{__('Open Hours')}}</h3>
        <div class="form-group">
            <label>
                <input type="checkbox" name="enable_open_hours" @if(!empty($row->meta->enable_open_hours)) checked @endif value="1"> {{__('Enable Open Hours')}}
            </label>
        </div>
        @php 
        if($row != null){$old=\Modules\Tour\Models\CategoryTimeSlot::where('tour_id',$row->id)->get();}else{$old=[];}
      
        @endphp
        <div class="table-responsive form-group" data-condition="enable_open_hours:is(1)">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{__('Enable?')}}</th>
                        <th>{{__('Open')}}</th>
                        <th>{{__('Close')}}</th>
                    </tr>
                </thead>
                 @for($i = 1 ; $i <=3 ; $i++) <tr>
                    <td>
                        <input style="display: inline-block" type="checkbox"name="open_hours[{{$i}}][enable]" value="1">
                    </td>
                    <td>
                        <select class="form-control" name="open_hours[{{$i}}][from]">
                            <?php
                            $time = strtotime('2023-01-01 00:00:00');
                            for ($k = 0; $k <= 23; $k++) :
                                $val = date('H:i:s', $time + 60 * 60 * $k);
                            ?>
                                <option @if(isset($old[$i -1]) && $old[$i -1]['start_at']==$val) selected @endif value="{{$val}}">{{$val}}</option>
                            <?php endfor; ?>
                        </select>
                    </td>
                    <td>
                        <select class="form-control" name="open_hours[{{$i}}][to]">
                            <?php
                            $time = strtotime('2023-01-01 00:00:00');
                            for ($k = 0; $k <= 23; $k++) :
                                $val = date('H:i:s', $time + 60 * 60 * $k);
                            ?>
                                <option @if(isset($old[$i -1]) && $old[$i - 1]['end_at']==$val) selected @endif value="{{$val}}">{{$val}}</option>
                            <?php endfor; ?>
                        </select>
                    </td>
                    </tr>
                    @endfor
            </table>
        </div>
    </div>
</div>