<tr>
    <td class="booking-history-type">
        @if($service = $booking->service)
        <i class="{{$service->getServiceIconFeatured()}}"></i>
        @endif
        <small>{{$booking->object_model}}</small>
    </td>
    <td>
        @if($service = $booking->service)
        @php
        $translation = $service->translate();
        @endphp
        <a target="_blank" href="{{$service->getDetailUrl()}}">
            {{$translation->title}}
        </a>
        @else
        {{__("[Deleted]")}}
        @endif
    </td>
    <td>
    @php
    $category_id = $service->category_id ?? 1;
    $category = \Modules\Tour\Models\TourCategory::find($category_id);
    $translate = $category ? $category->translate() : null;
        $url = $category ? $category->getDetailUrl() : '#';

    @endphp
    <a target="_blank" href="{{$url }}">
        @if ($translate)
            {{ $translate->name }}
        @else
            {{ __("[Deleted]") }}
        @endif
    </a>
</td>


@php 


$time=\Modules\Tour\Models\Bookning_dates::where('booking_id',$booking->id)->get();


if($time->count() ==1)
{

$start_at = $time[0]['start_from'];
$end_at = $time[0]['end_at'];
}else{
$start_at = $time[0]['start_from'];
$end_at = $time[1]['end_at'];
}




@endphp
     

    <td class="a-hidden">{{display_date($booking->created_at)}}</td>
    <td class="a-hidden">
        {{__("Booking Day")}} : {{display_date($booking->start_date)}} <br>
        
        {{__("Start At")}} :{{$start_at}}<br>
        {{__("End At")}} :{{$end_at}}
        
    </td>
    <td>{{format_money($booking->total)}}</td>
    <td>{{format_money($booking->paid)}}</td>
    <td>{{format_money($booking->total - $booking->paid)}}</td>
    <td class="{{$booking->status}} a-hidden">
        {{$booking->statusName}}
    </td>
    <td width="2%">
                        @if($service = $booking->service)
        <a class="btn btn-xs btn-primary btn-info-booking" data-ajax="{{route('booking.modal',['booking'=>$booking])}}" data-toggle="modal" data-id="{{$booking->id}}" data-target="#modal_booking_detail">
            <i class="fa fa-info-circle"></i>{{__("Details")}}
        </a>
        @endif

        <a href="{{route('user.booking.invoice',['code'=>$booking->code])}}" class="btn btn-xs btn-primary btn-info-booking open-new-window mt-1" onclick="window.open(this.href); return false;">
            <i class="fa fa-print"></i>{{__("Invoice")}}
        </a>
        @if($booking->status == 'unpaid')
        <a href="{{route('booking.checkout',['code'=>$booking->code])}}" class="btn btn-xs btn-primary btn-info-booking open-new-window mt-1">
            {{__("Pay now")}}
        </a>
        @endif
        
        @if($booking->customer_id == auth()->user()->id || ( $booking->service && $booking->service->author_id == auth()->user()->id)) 
        @if($booking->status !== 'completed' && $booking->status !== 'cancelled')
        <button class="btn btn-xs btn-primary btn-info-booking open-new-window mt-1" onclick="openCancelBookingModal('{{ route('user.cancel_booking', ['id' => $booking->id]) }}')">
            <i class="fa fa-times"></i>{{ __("Cancel") }}
        </button>
        @endif
        @endif

        @if($booking->vendor_id == auth()->user()->id)
        @if($booking->status == 'processing')
        <button class="btn btn-xs btn-primary btn-info-booking open-new-window mt-1" onclick="redirectTo('{{ route('vendor.edit_booking', ['id' => $booking->id]) }}')">
            <i class="fa fa-times"></i>{{ __("Update Booking") }}
        </button>
        @endif
        @endif

        <div id="message"></div>

    </td>


</tr>

<!-- Modal -->
<div class="modal fade" id="cancelBookingModal" tabindex="-1" aria-labelledby="cancelBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelBookingModalLabel">{{ __("Cancel Booking") }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{{ __("Are you sure you want to cancel this booking?") }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __("Close") }}</button>
                <button type="button" class="btn btn-danger" id="confirmCancelBooking">{{ __("Confirm") }}</button>
                <button type="button" style="display: none;" class="btn btn-danger" id="confirmCancelBookingWithRefunded">{{ __("Confirm") }}</button>
            </div>
        </div>
    </div>
</div>