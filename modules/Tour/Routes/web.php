<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Support\Facades\Route;
// Vendor Manage Tour
Route::group(['prefix'=>'user/'.config('tour.tour_route_prefix'),'middleware' => ['auth','verified']],function(){
    Route::get('/','ManageTourController@manageTour')->name('tour.vendor.index');
    Route::get('/create','ManageTourController@createTour')->name('tour.vendor.create');
    Route::get('/edit/{id}','ManageTourController@editTour')->name('tour.vendor.edit');
    Route::get('/del/{id}','ManageTourController@deleteTour')->name('tour.vendor.delete');
    Route::post('/store/{id}','ManageTourController@store')->name('tour.vendor.store');
    Route::get('bulkEdit/{id}','ManageTourController@bulkEditTour')->name("tour.vendor.bulk_edit");
    Route::get('clone/{id}','ManageTourController@cloneTour')->name("tour.vendor.clone");
    Route::get('/booking-report/bulkEdit/{id}','ManageTourController@bookingReportBulkEdit')->name("tour.vendor.booking_report.bulk_edit");
    Route::get('/recovery','ManageTourController@recovery')->name('tour.vendor.recovery');
    Route::get('/restore/{id}','ManageTourController@restore')->name('tour.vendor.restore');
});
Route::group(['prefix'=>'user/'.config('tour.tour_route_prefix')],function(){
    Route::group(['prefix'=>'availability'],function(){
        Route::get('/','AvailabilityController@index')->name('tour.vendor.availability.index');
        Route::get('/loadDates','AvailabilityController@loadDates')->name('tour.vendor.availability.loadDates');
        Route::get('/disabled_dates','AvailabilityController@disabled_dates')->name('tour.vendor.availability.disabled_dates');
        Route::post('/store','AvailabilityController@store')->name('tour.vendor.availability.store');
    });
});
// Tour
Route::group(['prefix'=>config('tour.tour_route_prefix')],function(){
    Route::get('/','\Modules\Tour\Controllers\TourController@index')->name('tour.search'); 
    Route::get('/{slug}','\Modules\Tour\Controllers\TourController@detail');
});
Route::get('/get-disabled-dates/{tourId}','AvailabilityController@get_disabled_dates')->name('get-disabled-dates');
Route::get('/time_slot/{start_date_html}/{tour_id}','AvailabilityController@time_slot')->name('time_slot');
Route::get('/all_menus/{tour_id}','AvailabilityController@all_menus')->name('all_menus');
Route::get('/disable_time_slot', 'AvailabilityController@disable_time_slot')->name('vendor.timeslots');
Route::post('/Store_disable_time', 'AvailabilityController@Store_disable_time_slot')->name('vendor.Store_disable_time_slot');
Route::post('/getEnabledTimeSlots', 'AvailabilityController@getEnabledTimeSlots')->name('vendor.getEnabledTimeSlots');
