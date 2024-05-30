@extends('layouts.user')
@section('content')
<h2 class="title-bar no-border-bottom">
    {{__("Booking History")}}
</h2>
@include('admin.message')
<div class="booking-history-manager">
    <div class="tabbable">
        <ul class="nav nav-tabs ht-nav-tabs">
            <?php $status_type = Request::query('status'); ?>
            <li class="@if(empty($status_type)) active @endif">
                <a href="{{route("user.booking_history")}}">{{__("All Booking")}}</a>
            </li>
            @if(!empty($statues))
            @foreach($statues as $status)
            <li class="@if(!empty($status_type) && $status_type == $status) active @endif">
                <a href="{{route("user.booking_history",['status'=>$status])}}">{{booking_status_to_text($status)}}</a>
            </li>
            @endforeach
            @endif
        </ul>

            <form action="{{ route('user.filter.booking_history') }}" method="Post">
                @csrf
            <div class="row">
                <div class="col-md-6">
                    
                <div class="form-group mr-2">
                    <label for="date_filter">{{ __('Filter by Date:') }}</label>
                    <input type="date" name="date_filter" id="date_filter" class="form-control" value="{{ Request::query('date_filter') }}">
                </div>
            </div>
                <div class="col-md-6">

                
                <div class="form-group">
                    <label for="category_id">{{ __('Filter by Category:') }}</label>
                    <select name="category_id" id="category_id" class="form-control">
                        <option value="">{{ __('All Categories') }}</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" @if(Request::query('category_filter')==$category->id) selected @endif>
                            {{ $category->name }} ({{$category->id}})
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            </div>
            
            <div class="row">
                <div class="col-md-6"><button type="submit" class="btn btn-primary">{{ __('Apply Filters') }}</button></div>
            </div>
            </form>



@if ($bookings->count() > 0)
        <div class="tab-content">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-booking-history">
                    <thead>
                        <tr>
                            <th width="2%">{{__("Type")}}</th>
                            <th>{{__("Title")}}</th>
                            <th>{{__("Category")}}</th>
                            <th class="a-hidden">{{__("Order Date")}}</th>
                            <th class="a-hidden">{{__("Execution Time")}}</th>
                            <th>{{__("Total")}}</th>
                            <th>{{__("Paid")}}</th>
                            <th>{{__("Remain")}}</th>
                            <th class="a-hidden">{{__("Status")}}</th>
                            <th>{{__("Action")}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        @endphp
                        @foreach($bookings as $booking)
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

                            <td class="a-hidden">{{display_date($booking->created_at)}}</td>
                            <td class="a-hidden">
                                {{__("Check in")}} : {{display_date($booking->start_date)}} <br>
                                {{__("Duration")}} : {{ $booking->getMeta("duration") ?? "1"  }} {{__("hours")}}
                            </td>
                            <td>{{format_money($booking->total)}}</td>
                            <td>{{format_money($booking->paid)}}</td>
                            <td>{{format_money($booking->total - $booking->paid)}}</td>
                            <td class="{{$booking->status}} a-hidden">
                                {{$booking->statusName}}
                            </td>
                            <td width="2%">

                                <a href="{{route('user.booking.invoice',['code'=>$booking->code])}}" class="btn btn-xs btn-primary btn-info-booking open-new-window mt-1" onclick="window.open(this.href); return false;">
                                    <i class="fa fa-print"></i>{{__("Invoice")}}
                                </a>
                                @if($booking->status == 'unpaid')
                                <a href="{{route('booking.checkout',['code'=>$booking->code])}}" class="btn btn-xs btn-primary btn-info-booking open-new-window mt-1">
                                    {{__("Pay now")}}
                                </a>
                                @endif
                                <!-- @if($booking->status !== 'completed' && $booking->status !== 'cancelled' )
        <a href="{{ route('user.cancel_booking', ['id' => $booking->id]) }}" class="btn btn-xs btn-primary btn-info-booking open-new-window mt-1" onclick="cancelBooking('{{ route('user.cancel_booking', ['id' => $booking->id]) }}')">
            <i class="fa fa-times"></i>{{ __("Cancel") }}
        </a>
        @endif -->

                                @if($booking->status !== 'completed' && $booking->status !== 'cancelled')
                                <button class="btn btn-xs btn-primary btn-info-booking open-new-window mt-1" onclick="openCancelBookingModal('{{ route('user.cancel_booking', ['id' => $booking->id]) }}')">
                                    <i class="fa fa-times"></i>{{ __("Cancel") }}
                                </button>
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
                        </div> @endforeach
                    </tbody>
                </table>
            </div>
            <div class="bravo-pagination">

            </div>
        </div>
        @else
        {{__("No Booking History")}}
        @endif
    </div>
    <div class="modal" tabindex="-1" id="modal_booking_detail">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Booking ID: #')}} <span class="user_id"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-center">{{__("Loading...")}}</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $('#modal_booking_detail').on('show.bs.modal', function(e) {
        var btn = $(e.relatedTarget);
        $(this).find('.user_id').html(btn.data('id'));
        $(this).find('.modal-body').html('<div class="d-flex justify-content-center">{{__("Loading...")}}</div>');
        var modal = $(this);
        $.get(btn.data('ajax'), function(html) {
            modal.find('.modal-body').html(html);
        })
    })



    function redirectTo(url) {
        window.location.href = url;
    }
</script>

<script>
    function openCancelBookingModal(cancelUrl) {
        document.getElementById('confirmCancelBooking').setAttribute('onclick', `cancelBooking('${cancelUrl}')`);

        $('#cancelBookingModal').modal('show');
    }

    function cancelBooking(cancelUrl) {
        $.ajax({
            url: cancelUrl,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#cancelBookingModal .modal-body').html('<p>Booking cancelled successfully!</p>');
                    setTimeout(function() {
                        $('#cancelBookingModal').modal('hide');
                        location.reload();

                    }, 4000);
                } else {
                    $('#cancelBookingModal .modal-body').html('<p>' + response.message + '</p>');
                    $('#confirmCancelBooking').css('display', 'none');
                    $('#confirmCancelBookingWithRefunded').css('display', 'block');

                    $('#confirmCancelBookingWithRefunded').on('click', function() {
                        $.ajax({
                            url: cancelUrl,
                            type: 'GET',
                            data: {
                                refunded: 1
                            },
                            success: function(newResponse) {
                                $('#cancelBookingModal .modal-body').html('<p>Booking cancelled successfully!</p>');
                                setTimeout(function() {
                                    $('#cancelBookingModal').modal('hide');
                                    location.reload();
                                }, 4000);
                            },
                            error: function(newError) {
                                $('#cancelBookingModal').modal('hide');
                            }
                        });
                    });
                }
            },
            error: function(error) {
                $('#cancelBookingModal').modal('hide');

            }
        });
    }
</script>

@endpush