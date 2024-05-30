<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Modules\Booking\Models\Booking;
use Modules\Tour\Models\Bookning_dates;

class Firebase_Hourly extends Command
{
    protected $signature = 'Firebase_Hourly';
    protected $description = 'FireBase Notification Every Hour';
    public function handle()
    {
        $currentDate = Carbon::now()->toDateString();
        $all_booking = DB::table('bravo_bookings')
            ->where('status', 'processing')
            ->whereDate('start_date', $currentDate)
            ->get();
        foreach ($all_booking as $booking) {
            $currentDateTime = Carbon::now();
            $oneHourLater = $currentDateTime->addHour();
            $bookingDate = Bookning_dates::where('booking_id', $booking->id)->whereTime('start_from', '>', $oneHourLater)->first();
            $user = User::where('id', $booking->customer_id)->first();
            $token = $user->token;
            if ($token != null) {
                $data = [
                    $booking->id,
                ];
                $content = "سيتم بدأ الرحلة الخاصة بكم بعد ساعة من الان";
                $content_en = "Your Flight will Start In an Hour From Now";
                $send = notifybyfirebase($content, $content_en, $token, $data);
            }
        }
    }
}
