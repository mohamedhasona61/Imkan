<div class="form-checkout" id="form-checkout" >
    <input type="hidden" name="code" value="{{$booking->code}}">
    <div class="form-section">
        <div class="row">

            <div class="col-md-6">
                <div class="form-group">
                    <label >{{__("First Name")}} <span class="required">*</span></label>
                    <input type="text" placeholder="{{__("First Name")}}" class="form-control" value="{{$user->first_name ?? ''}}" name="first_name">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label >{{__("Last Name")}} <span class="required">*</span></label>
                    <input type="text" placeholder="{{__("Last Name")}}" class="form-control" value="{{$user->last_name ?? ''}}" name="last_name">
                </div>
            </div>
            <div class="col-md-6 field-email">
                <div class="form-group">
                    <label >{{__("Email")}} <span class="required">*</span></label>
                    <input type="email" placeholder="{{__("email@domain.com")}}" class="form-control" value="{{$user->email ?? ''}}" name="email">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label >{{__("Phone")}} <span class="required">*</span></label>
                    <input type="text" placeholder="{{__("Your Phone")}}" class="form-control" value="{{$user->phone ?? ''}}" name="phone">
                </div>
            </div>



  
            <div class="col-md-12">
                <label >{{__("Special Requirements")}} </label>
                <textarea name="customer_notes" cols="30" rows="6" class="form-control" placeholder="{{__('Special Requirements')}}"></textarea>
            </div>
        </div>
    </div>

    @include ('Booking::frontend/booking/checkout-passengers')
    @include ('Booking::frontend/booking/checkout-deposit')
    @include ($service->checkout_form_payment_file ?? 'Booking::frontend/booking/checkout-payment')

    @php
    $term_conditions = setting_item('booking_term_conditions');
    @endphp

    <div class="form-group">
        <label class="term-conditions-checkbox">
            <input type="checkbox" name="term_conditions"> {{__('I have read and accept the')}}  <a target="_blank" href="{{get_page_url($term_conditions)}}">{{__('terms and conditions')}}</a>
        </label>
    </div>
    @if(setting_item("booking_enable_recaptcha"))
        <div class="form-group">
            {{recaptcha_field('booking')}}
        </div>
    @endif
    <div class="html_before_actions"></div>

    <p class="alert-text mt10" v-show=" message.content" v-html="message.content" :class="{'danger':!message.type,'success':message.type}"></p>

    <div class="form-actions">
        <button class="btn btn-danger" @click="doCheckout">{{__('Submit')}}
            <i class="fa fa-spin fa-spinner" v-show="onSubmit"></i>
        </button>
    </div>
</div>
