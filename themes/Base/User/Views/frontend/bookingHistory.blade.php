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
        @if(auth()->user()->role_id ==1 || auth()->user()->role_id ==2)
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
        @endif
        @if(!empty($bookings) and $bookings->total() > 0)
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
                        @include(ucfirst($booking->object_model).'::frontend.bookingHistory.loop')
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="bravo-pagination">
                {{$bookings->appends(request()->query())->links()}}
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