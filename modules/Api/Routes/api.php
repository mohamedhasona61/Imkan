<?php

use Illuminate\Http\Request;
use \Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/* Config */
Route::get('configs','BookingController@getConfigs')->name('api.get_configs');
/* Service */
Route::get('services','SearchController@searchServices')->name('api.service-search');
Route::get('{type}/search','SearchController@search')->name('api.search2');
Route::get('{type}/detail/{id}','SearchController@detail')->name('api.detail');
Route::get('{type}/availability/{id}','SearchController@checkAvailability')->name('api.service.check_availability');
Route::get('boat/availability-booking/{id}','SearchController@checkBoatAvailability')->name('api.service.checkBoatAvailability');

Route::get('{type}/filters','SearchController@getFilters')->name('api.service.filter');
Route::get('{type}/form-search','SearchController@getFormSearch')->name('api.service.form');

Route::group(['middleware' => 'api'],function(){
    Route::post('{type}/write-review/{id}','ReviewController@writeReview')->name('api.service.write_review');
});


/* Layout HomePage */
Route::get('home-page','BookingController@getHomeLayout')->name('api.get_home_layout');

/* Register - Login */
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('login', 'AuthController@login')->middleware(['throttle:login']);
    Route::post('register', 'AuthController@register');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('me', 'AuthController@me');
    Route::post('me', 'AuthController@updateUser');
    Route::post('change-password', 'AuthController@changePassword');

});

Route::group(['prefix' => 'user', 'middleware' => ['api'],], function ($router) {
    Route::get('booking-history', 'UserController@getBookingHistory')->name("api.user.booking_history");
    Route::post('/wishlist','UserController@handleWishList')->name("api.user.wishList.handle");
    Route::get('/wishlist','UserController@indexWishlist')->name("api.user.wishList.index");
    Route::post('/permanently_delete','UserController@permanentlyDelete')->name("user.permanently.delete");
});

/* Location */
Route::get('locations','LocationController@search')->name('api.location.search');
Route::get('location/{id}','LocationController@detail')->name('api.location.detail');

// Bookingreview_service
Route::group(['prefix'=>config('booking.booking_route_prefix')],function(){
    Route::post('/addToCart','BookingController@addToCart')->name("api.booking.add_to_cart");
    Route::post('/addEnquiry','BookingController@addEnquiry')->name("api.booking.add_enquiry");
    Route::post('/doCheckout','BookingController@doCheckout')->name('api.booking.doCheckout');
    Route::get('/confirm/{gateway}','BookingController@confirmPayment');
    Route::get('/cancel/{gateway}','BookingController@cancelPayment');
    Route::get('/{code}','BookingController@detail');
    Route::get('/{code}/thankyou','BookingController@thankyou')->name('booking.thankyou');
    Route::get('/{code}/checkout','BookingController@checkout');
    Route::get('/{code}/check-status','BookingController@checkStatusCheckout');
});

// Gateways
Route::get('/gateways','BookingController@getGatewaysForApi');

// News
Route::get('news','NewsController@search')->name('api.news.search');
Route::get('news/category','NewsController@category')->name('api.news.category');
Route::get('news/{id}','NewsController@detail')->name('api.news.detail');

/* Media */
Route::group(['prefix'=>'media','middleware' => 'auth:api'],function(){
    Route::post('/store','MediaController@store')->name("api.media.store");
});
Route::get('/categories','MediaController@category')->name('all_categories');
Route::get('/location','MediaController@location')->name('all_locations');
Route::get('/tours','MediaController@tours')->name('tours');
Route::get('/get_tours/{location_id}/{category_id}','MediaController@get_tours')->name('get_tours');

Route::get('/get_images/{tour_id}','MediaController@get_images')->name('get_images');
Route::post('/add_to_wishlist','MediaController@add_to_wishlist')->name('add_to_wishlist'); 
Route::post('/remove_from_wishlist','MediaController@remove_from_wishlist')->name('remove_from_wishlist'); 
Route::get('/getFavorite/{user_id}','MediaController@getFavorite')->name('getFavorite'); 
Route::get('/get_coupons/{id}/{booking_id}/{service_id}', 'MediaController@get_coupons')->name('get_coupons');
Route::get('/getTours/{tour_id}', 'MediaController@tour_detail')->name('tour_detail');

Route::get('/category_time', 'MediaController@category_time')->name('category_time');
Route::get('/tour_time/{id}', 'MediaController@tour_time')->name('tour_time');
Route::get('/time_slot/{day}/{tour_id}', 'MediaController@time_slot')->name('time_slot');
Route::get('/all_menus/{id}', 'MediaController@all_menus')->name('all_menus');
Route::post('/new_booking', 'MediaController@new_booking')->name('new_booking');
Route::post('/update_booking', 'MediaController@update_booking')->name('update_booking');
Route::get('/category_by_time/{time}/{id}', 'MediaController@category_by_time')->name('category_by_time');
Route::get('/get_all_reviews/{id}', 'MediaController@get_all_reviews')->name('get_all_reviews');
Route::get('/all_booking/{id}', 'MediaController@all_booking')->name('all_booking');
Route::post('/add_review', 'MediaController@add_review')->name('add_review');
Route::get('/tours_search/{search_query}/{category_id}/{lang}', 'MediaController@search')->name('search');
Route::post('/cancel_booking', 'MediaController@cancel_booking')->name('cancel_booking');
Route::get('/get_deposit/{id}', 'MediaController@get_deposit')->name('get_deposit');
Route::post('/delete_account', 'MediaController@delete_account')->name('delete_account');

Route::get('/check_activation/{id}', 'MediaController@check_activation')->name('check_activation');

