<?php

namespace App\Http\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Modules\Booking\Events\BookingCreatedEvent;
use Modules\Booking\Models\Booking;
use Modules\Coupon\Models\CouponBookings;
use Modules\Tour\Models\Bookning_dates;

class MyFatooraServices


{
    private $base_url;
    private $headers;
    private $request_client;



    public function __construct(Client $request_client)
    {
        $this->request_client = $request_client;
        $this->base_url = env('Myfatoorah_base_url');
        $this->headers = [
            'Content-type' => 'application/json',
            'authorization' => 'Bearer ' . env('Myfatoora_token')
        ];
    }
    private function buildRequest($url, $method, $data = [])
    {
        $request = new Request($method, $this->base_url . $url, $this->headers);
        if (!$data)
            return false;

        $response = $this->request_client->send($request, [
            'json' => $data,
        ]);

        if ($response->getStatusCode() != 200) {
            return false;
        }
        $response = json_decode($response->getBody(), true);
        response()->json([
            'url' => $response['Data']['InvoiceURL'],
        ])->send();
    }
    public function sendPayment($data)
    {
       $response = $this->buildRequest('v2/SendPayment', 'POST', $data);
    }


    public function getPaymentStatus($data, $code)
    {
        $request = new Request('POST', $this->base_url . 'v2/getPaymentStatus', $this->headers);
        if (!$data)
            return false;

        $response = $this->request_client->send($request, [
            'json' => $data,
        ]);

        if ($response->getStatusCode() != 200) {
            return false;
        }
        $response = json_decode($response->getBody(), true);
        $booking = Booking::where('code', $code)->first();

        if ($response['Data']['InvoiceStatus'] == "Paid") {
            $booking->is_paid = 1;
            $booking->paid=$booking->pay_now;
            $booking->status = "processing";
            $booking->save();
            $booking_dates = Bookning_dates::where('booking_id', $booking->id)->get();
            foreach ($booking_dates as $booking_dates) {
                $booking_dates->active = 1;
                $booking_dates->save();
            }
            $coupon_booking = CouponBookings::where('booking_id', $booking->id)->first();
            if ($coupon_booking) {
                $coupon_booking->booking_status = "processing";
                $coupon_booking->save();
            }
            event(new BookingCreatedEvent($booking));
            return redirect()->route('booking.success', ['code' => $booking->code]);
        } else {
            return redirect()->route('booking.checkout', ['code' => $booking->code]);
        }
    }
}
