<div class="booking-review">
    <h4 class="booking-review-title">{{__('Your Information')}}</h4>
    <div class="booking-review-content">
        <div class="review-section">
            <div class="info-form">
                <ul>
                    <li class="info-first-name">
                        <div class="label">{{__('First name')}}</div>
                        <div class="val">{{$booking->first_name}}</div>
                    </li>
                    <li class="info-last-name">
                        <div class="label">{{__('Last name')}}</div>
                        <div class="val">{{$booking->last_name}}</div>
                    </li>
                    @if($booking->Email1=null)
                    <li class="info-email">
                        <div class="label">{{__('Email')}}</div>
                        <div class="val">{{$booking->email}}</div>
                    </li>
                    @endif
                    <li class="info-phone">
                        <div class="label">{{__('Phone')}}</div>
                        <div class="val">{{$booking->phone}}</div>
                    </li>
                    
                    @if($booking->customer_notes!=null)
        
                    <li class="info-notes">
                        <div class="label">{{__('Special Requirements')}}</div>
                        <div class="val">{{$booking->customer_notes}}</div>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
