<?php

namespace Modules\Booking\Gateways;

use App\Http\Services\MyFatooraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Booking\Events\BookingCreatedEvent;

class MyFatooraGateway extends BaseGateway
{
    public $name = 'MyFatoora';
    public $is_offline =  true;

    public function process(Request $request, $booking, $service)
    {
        $service->beforePaymentProcess($booking, $this);


        if ($booking->paid <= 0) {
            $booking->status = $booking::PROCESSING;
        } else {
            if ($booking->paid < $booking->total) {
                $booking->status = $booking::PARTIAL_PAYMENT;
            } else {
                $booking->status = $booking::PAID;
            }
        }

        $booking->save();
        try {
            event(new BookingCreatedEvent($booking));
        } catch (\Swift_TransportException $e) {
            Log::warning($e->getMessage());
        }

        $service->afterPaymentProcess($booking, $this);
        return response()->json([
            'url' => $booking->getDetailUrl()
        ])->send();
    }



    public function getOptionsConfigs()
    {
        return [
            [
                'type'  => 'checkbox',
                'id'    => 'enable',
                'label' => __('Enable MyFatoora?')
            ],
            [
                'type'  => 'checkbox',
                'id'    => 'live',
                'label' => __('Enable MyFatoora Live?')
            ],
            [
                'type'  => 'input',
                'id'    => 'myfatoora_token',
                'label' => __('MyFatooraToken'),
                'std'   => __("MyFatoora"),
                'multi_lang' => "0"
            ],

        ];
    }
}
