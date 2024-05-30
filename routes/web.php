<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Modules\Core\Models\Settings;
use Modules\Tour\Models\CategoryTimeSlot;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Modules\Review\Models\ReviewMeta;
use Carbon\Carbon;
use Modules\Tour\Models\TourDate;
use Modules\Booking\Models\Booking;
use Modules\Tour\Models\Bookning_dates;
use Modules\Tour\Models\Tour;

use App\Helpers\FireBaseNotifications;
use Modules\Coupon\Models\CouponBookings;

Route::get('/intro', 'LandingpageController@index');
Route::get('/', 'HomeController@index')->name('website');
Route::get('/home', 'HomeController@index')->name('home');
Route::post('/install/check-db', 'HomeController@checkConnectDatabase');
Route::get('test_service',function(){

    $booking=Booking::latest()->first();

     $booking->service->service_fee[0]['price'];
    $tour_total=$booking->service->price;

    return $booking;

});

// Social Login
Route::get('social-login/{provider}', 'Auth\LoginController@socialLogin');
Route::get('social-callback/{provider}', 'Auth\LoginController@socialCallBack');

// Logs
Route::get(config('admin.admin_route_prefix') . '/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->middleware(['auth', 'dashboard', 'system_log_view'])->name('admin.logs');

Route::get('/install', 'InstallerController@redirectToRequirement')->name('LaravelInstaller::welcome');
Route::get('/install/environment', 'InstallerController@redirectToWizard')->name('LaravelInstaller::environment');
Route::get('/test', function () {

    $user = User::find(15);
    $token = $user->token;
    $content_en = "Your reservation has been sent Successfully. Please confirm your reservation";

    $content = "لقد تم ارسال الحجز الخاص بك بنجاح برجاء تأكيد الحجز" . '+' . $content_en;


    $data = [
        200
    ];
    $title = "Imkan";
    $SERVER_API_KEY = "AAAAIgc53vk:APA91bG-b2eB7RaUcBe0YBjs9WEkzJzZqjsr9pSTiXcB1_sUebmOVakLEk_brHQFVwvbXW4eookrbExEFNLOIdJfzs1G7lrYCXbow-EOEtehPjJO10z6Qvq79NllBeIBMrPBBcd2bWwD";
    $data = [
        "registration_ids" => [
            "dWB7qTmjS26D3ewzY83FOS:APA91bF7vVyeTVS-cS23xAriAN9HnZheRJWw1AvhyoiBSg3R8pk7qUqxB2TbHxD7niEoCx6MdHOGiXyCAtE77XlXVbjZ_Q1fl0SbNKqnJPz-sWmzd9xl-jX9CjZajl-Mhjd8B6ecIsH6"
        ],
        "notification" => [
            "title" => $title,
            "content" => $content,
            "body" => $content,
            "content_en" => $content_en,
            "body_en" => $content_en,
            "sound" => "default"
        ],
    ];


    $dataString = json_encode($data);
    $headers = [
        'Authorization: key=' . $SERVER_API_KEY,
        'Content-Type: application/json',
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
    $response = curl_exec($ch);
    return $response;
    $serverKey = 'AAAAIgc53vk:APA91bG-b2eB7RaUcBe0YBjs9WEkzJzZqjsr9pSTiXcB1_sUebmOVakLEk_brHQFVwvbXW4eookrbExEFNLOIdJfzs1G7lrYCXbow-EOEtehPjJO10z6Qvq79NllBeIBMrPBBcd2bWwD';

    $deviceToken = 'eqdJ8r_vTqCWQpfJtBGW1j:APA91bF_kkC4syoq1kzf-U900OElAyNQomJm-vvcdTeJVItvd2Ncw-M1EmBXAi_NfIzu_HMDhnOzZbJErWjJMBVD2RtqE4Azq9lQYGwRANtw6p6L5HtTbp6RbUKquD-3RcwZrl6V_9jx';

    $notification = [
        'title' => 'Title',
        'body' => 'Body',
    ];

    $data = [
        'key1' => 'value1',
        'key2' => 'value2',
    ];

    $payload = [
        'to' => $deviceToken,
        'notification' => $notification,
        'data' => $data,
    ];

    $headers = [
        'Authorization: key=' . $serverKey,
        'Content-Type: application/json',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $result = curl_exec($ch);
    curl_close($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    return $result;

    return 'Notification sent successfully!';
});



Route::get('en/test_language', function (Request $request) {
    $url = $request->url();
    $queryString = $request->getQueryString();

    // Check if "en" is present in the query string
    if (strpos($queryString, 'en=') !== false) {
        echo "The URL contains 'en' as a language.";
    } else {
        echo "The URL does not contain 'en' as a language.";
    }
});
