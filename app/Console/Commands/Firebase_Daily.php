<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Modules\Booking\Models\Booking;
use Modules\Tour\Models\Bookning_dates;

class Firebase_Daily extends Command
{
    protected $signature = 'Firebase_Daily';
    protected $description = 'Firebase_Daily';
    public function handle()
    {
        $all_booking = DB::table('bravo_bookings')->where('status', 'processing')->get();
        foreach ($all_booking as $booking) {
            $currentDateTime = Carbon::now();
            $twentyFourHoursLater = $currentDateTime->addHours(24);
            $bookingDate = Bookning_dates::where('booking_id', $booking->id)
                ->whereTime('start_from', '>', $twentyFourHoursLater)
                ->first();
            $user = User::where('id', $booking->customer_id)->first();
            $token = $user->token;
            if ($token != null) {
                $data = [
                    $booking->id,
                ];
                $currentDateTime = Carbon::now();
                $hour = $currentDateTime->format('H');

                $content = "سيتم بدأ الرحلة الخاصة بكم غدا في تمام الساعة $hour";

                $content_en = "Your Flight will Start In $hour";
                $send = notifybyfirebase($content, $content_en, $token, $data);
            }
        }
    }
}
