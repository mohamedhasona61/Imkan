<?php
$translation = $service->translate();
$lang_local = app()->getLocale();
?>
<div class="b-panel-title">{{__('Tour information')}}</div>
<div class="b-table-wrap">
    <table class="b-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="label">{{__('Booking Number')}}</td>
            <td class="val">#{{$booking->id}}</td>
        </tr>
        <tr>
            <td class="label">{{__('Booking Status')}}</td>
            <td class="val">{{$booking->statusName}}</td>
        </tr>
        @if($booking->gatewayObj)
        <tr>
            <td class="label">{{__('Payment method')}}</td>
            <td class="val">{{$booking->gateway}}</td>
        </tr>
        @endif
        @if($booking->gatewayObj and $note = $booking->gatewayObj->getOption('payment_note'))
        <tr>
            <td class="label">{{__('Payment Note')}}</td>
            <td class="val">{!! clean($note) !!}</td>
        </tr>
        @endif
        <tr>
            <td class="label">{{__('Tour name')}}</td>
            <td class="val">
                <a href="{{$service->getDetailUrl()}}">{!! clean($translation->title) !!}</a>
            </td>

        </tr>
        <tr>
            @if($translation->address)
            <td class="label">{{__('Address')}}</td>
            <td class="val">
                {{$translation->address}}
            </td>
            @endif
        </tr>
        @if($booking->start_date && $booking->end_date)

        @php
        @endphp
        <tr>
            <td class="label">{{__('Start From')}}</td>
            <td class="val">{{$start_from}}</td>
        </tr>
        <tr>
            <td class="label">{{__('End At')}}</td>
            <td class="val">{{$end_at}}</td>
        </tr>

        <tr>
            <td class="label">{{__('Duration:')}}</td>
            <td class="val">
                {{human_time_diff($end_at,$start_from)}}
            </td>
        </tr>
        @endif

        @php
        $person_types = $booking->getJsonMeta('person_types')
        @endphp
        @if(!empty($person_types))
        <tr>
            <td class="label">{{__("Guests")}}:</td>
            <td class="val">
                <strong>{{$person_types[0]['number']}}</strong>
            </td>
        </tr>
        @endif

        @php
        $menus = $booking->getJsonMeta('menus');
        if (is_string($menus)) {
        $menus = json_decode($menus, true);
        }
        @endphp

        @if (!empty($menus) && count($menus) > 0)
        @foreach ($menus as $menu)
        @php
        $menu = json_encode($menu);
        $menu = json_decode($menu);

        if ($menu->submenuId== $menu->itemId) {
        $item = \Modules\Core\Models\MenuTour::find($menu->submenuId);
        } else {
        $item = \Modules\Core\Models\Terms::find($menu->itemId);
        }
        @endphp
        <tr>
            <td class="label">{{ $item->name }}:</td>
            <td class="val">
                <strong>{{ $menu->count }}</strong>
            </td>
        </tr>
        @endforeach
        @endif




        @php
        $extra_price = $booking->getJsonMeta('extra_price');
        @endphp
        @if (!empty($extra_price) && count($extra_price) > 0)

        @foreach ($extra_price as $extra_price)
        @php
        $extra_price = json_encode($extra_price);
        $extra_price = json_decode($extra_price);
        @endphp
        <tr>
            <td class="label">{{ $extra_price->name }}:</td>
            <td class="val">
                <strong>{{ $extra_price->price }}</strong>
            </td>
        </tr>
        @endforeach
        @endif




        @php
        $extra_price = $booking->getJsonMeta('extra_price');
        @endphp
        @if (!empty($extra_price) && count($extra_price) > 0)

        @foreach ($extra_price as $extra_price)
        @php
        $extra_price = json_encode($extra_price);
        $extra_price = json_decode($extra_price);
        @endphp
        <tr>
            <td class="label">{{ $extra_price->name }}:</td>
            <td class="val">
                <strong>{{ $extra_price->price }}</strong>
            </td>
        </tr>
        @endforeach
        @endif
        @if($booking->coupon_amount !== 0)
        <tr>
            <td class="label fsz21">{{__('Coupon')}}</td>
            <td class="val fsz21"><strong style="color: #FA5636">{{format_money($booking->coupon_amount)}}</strong></td>
        </tr>
        @endif



        <tr>
            <td class="label fsz21">{{__('Total')}}</td>
            <td class="val fsz21"><strong style="color: #FA5636">{{format_money($booking->total)}}</strong></td>
        </tr>
        <tr>
            <td class="label fsz21">{{__('Paid')}}</td>
            <td class="val fsz21"><strong style="color: #FA5636">{{format_money($booking->paid)}}</strong></td>
        </tr>
        @if($booking->total > $booking->paid)
        <tr>
            <td class="label fsz21">{{__('Remain')}}</td>
            <td class="val fsz21"><strong style="color: #FA5636">{{format_money($booking->total - $booking->paid)}}</strong></td>
        </tr>
        @endif
    </table>
</div>
<div class="text-center mt20">
    <a href="{{ route("user.booking_history") }}" target="_blank" class="btn btn-primary manage-booking-btn">{{__('Manage Bookings')}}</a>
</div>