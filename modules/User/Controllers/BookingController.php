<?php

namespace Modules\User\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Matrix\Exception;
use Modules\FrontendController;
use Modules\User\Events\NewVendorRegistered;
use Modules\User\Events\SendMailUserRegistered;
use Modules\User\Models\Newsletter;
use Modules\User\Models\Subscriber;
use Modules\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\MessageBag;
use Modules\Vendor\Models\VendorRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Validator;
use Modules\Booking\Models\Booking;
use App\Helpers\ReCaptchaEngine;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use Modules\Booking\Models\Enquiry;
use Modules\Tour\Models\Bookning_dates;

class BookingController extends FrontendController
{

    public function bookingInvoice($code)
    {
        $booking = Booking::where('code', $code)->first();

        $booking_date = Bookning_dates::where('booking_id', $booking->id)->get();


        if (count($booking_date) == 1) {
            $start_from = $booking_date[0]['start_from'];
            $end_at = $booking_date[0]['end_at'];
        } else {
            $start_from = $booking_date[0]['start_from'];
            $end_at = $booking_date[1]['end_at'];
        }

        $booking_meta=DB::table('bravo_booking_meta')->where('booking_id',$booking->id)->get();


        $user_id = $booking->customer_id;
        if (empty($booking)) {
            return redirect('user/booking-history');
        }
        if ($booking->customer_id != $user_id and $booking->vendor_id != $user_id) {
            return redirect('user/booking-history');
        }
        $data = [
            'booking'    => $booking,
            'start_from'    => $start_from,
            'end_at'    => $end_at,
            'service'    => $booking->service,
            'page_title' => __("Invoice")
        ];
        return view('User::frontend.bookingInvoice', $data);
    }
    public function ticket($code = '')
    {
        $booking = Booking::where('code', $code)->first();
        $user_id = Auth::id();
        if (empty($booking)) {
            return redirect('user/booking-history');
        }
        if ($booking->customer_id != $user_id and $booking->vendor_id != $user_id) {
            return redirect('user/booking-history');
        }
        $data = [
            'booking'    => $booking,
            'service'    => $booking->service,
            'page_title' => __("Ticket")
        ];
        return view('User::frontend.booking.ticket', $data);
    }
}
