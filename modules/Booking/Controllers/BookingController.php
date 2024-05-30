<?php

namespace Modules\Booking\Controllers;

use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Mockery\Exception;
use Modules\Booking\Events\BookingCreatedEvent;
use Modules\Booking\Events\BookingUpdatedEvent;
use Modules\Booking\Events\EnquirySendEvent;
use Modules\Booking\Events\SetPaidAmountEvent;
use Modules\Booking\Models\BookingPassenger;
use Modules\User\Events\SendMailUserRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\Enquiry;
use App\Helpers\ReCaptchaEngine;
use App\Http\Services\MyFatooraServices;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Settings;
use Modules\Tour\Models\Bookning_dates;
use Modules\Tour\Models\Tour;
use Illuminate\Support\Facades\URL;
use Modules\Core\Models\ExtrasTranslation;
use Modules\Core\Models\Menu;
use Modules\Core\Models\MenuTour;
use Modules\Core\Models\MenusTranslation;
use Modules\Core\Models\MenuTranslation;
use Modules\Core\Models\Terms;
use Modules\Core\Models\TermsTranslation;
use Modules\Media\Models\MediaFile;
use Modules\Tour\Models\MenuExtras;
use Modules\Tour\Models\TourTranslation;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace;

class BookingController extends \App\Http\Controllers\Controller
{
    use AuthorizesRequests;
    protected $booking;
    protected $enquiryClass;
    protected $bookingInst;

    private $fatooraaservice;

    public function __construct(Booking $booking, Enquiry $enquiryClass, MyFatooraServices $fatooraaservice)
    {
        $this->booking = $booking;
        $this->enquiryClass = $enquiryClass;
        $this->fatooraaservice = $fatooraaservice;
    }

    protected function validateCheckout($code)
    {

        if (!is_enable_guest_checkout() and !Auth::check()) {
            $error = __("You have to login in to do this");
            if (\request()->isJson()) {
                return $this->sendError($error)->setStatusCode(401);
            }
            return redirect(route('login', ['redirect' => \request()->fullUrl()]))->with('error', $error);
        }

        $booking = $this->booking::where('code', $code)->first();

        $this->bookingInst = $booking;

        if (empty($booking)) {
            abort(404);
        }
        if (!is_enable_guest_checkout() and $booking->customer_id != Auth::id()) {
            abort(404);
        }
        return true;
    }

    public function checkout($code)
    {
        $res = $this->validateCheckout($code);
        if ($res !== true) return $res;

        $booking = $this->bookingInst;

        if (!in_array($booking->status, ['draft', 'unpaid'])) {
            return redirect('/');
        }
        $menus = $booking->getJsonMeta('menus');

        $selectedItems = [];

        if ($menus) {
            foreach ($menus as $menu) {
                if ($menu['itemId'] == $menu['submenuId']) {
                    $item = MenuTour::find($menu['submenuId']);
                    $item->count = $menu['count'];
                    $item_en = MenusTranslation::where('origin_id', $item->id)->where('locale', 'en')->first();
                    $item->name_en = $item_en->name ?? '';
                } else {
                    $item = Terms::where('id', $menu['itemId'])->first();
                    $item->count = $menu['count'];
                    $item_en = TermsTranslation::where('origin_id', $item->id)->where('locale', 'en')->first();
                    $item->name_en = $item_en->name ?? '';
                }
                $selectedItems[] = $item;
            }
        }

        $is_api = request()->segment(1) == 'api';

        $data = [
            'page_title' => __('Checkout'),
            'booking'    => $booking,
            'service'    => $booking->service,
            'gateways'   => $this->getGateways(),
            'user'       => auth()->user(),
            'is_api'    =>  $is_api,
            'selectedItems' => $selectedItems,
        ];
        return view('Booking::frontend/checkout', $data);
    }

    public function checkStatusCheckout($code)
    {
        $booking = $this->booking::where('code', $code)->first();
        $data = [
            'error'    => false,
            'message'  => '',
            'redirect' => ''
        ];
        if (empty($booking)) {
            $data = [
                'error'    => true,
                'redirect' => url('/')
            ];
        }
        if (!is_enable_guest_checkout() and $booking->customer_id != Auth::id()) {
            $data = [
                'error'    => true,
                'redirect' => url('/')
            ];
        }
        if (!in_array($booking->status, ['draft', 'unpaid'])) {
            $data = [
                'error'    => true,
                'redirect' => url('/')
            ];
        }
        return response()->json($data, 200);
    }
    protected function validateDoCheckout()
    {

        $request = \request();
        if (!is_enable_guest_checkout() and !Auth::check()) {
            return $this->sendError(__("You have to login in to do this"))->setStatusCode(401);
        }

        if (auth()->user() && !auth()->user()->hasVerifiedEmail() && setting_item('enable_verify_email_register_user') == 1) {
            return $this->sendError(__("You have to verify email first"), ['url' => url('/email/verify')]);
        }
        /**
         * @param Booking $booking
         */
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('', ['errors' => $validator->errors()]);
        }
        $code = $request->input('code');

        $booking = $this->booking::where('code', $code)->first();
        $this->bookingInst = $booking;

        if (empty($booking)) {
            abort(404);
        }
        if (!is_enable_guest_checkout() and $booking->customer_id != Auth::id()) {
            abort(404);
        }
        return true;
    }

    public function doCheckout(Request $request)
    {

        $res = $this->validateDoCheckout();
        if ($res !== true) return $res;
        $user = auth()->user();

        $booking = $this->bookingInst;

        if (!in_array($booking->status, ['draft', 'unpaid'])) {
            return $this->sendError('', [
                'url' => $booking->getDetailUrl()
            ]);
        }
        $service = $booking->service;
        if (empty($service)) {
            return $this->sendError(__("Service not found"));
        }

        $is_api = request()->segment(1) == 'api';


        if (!$is_api and ReCaptchaEngine::isEnable() and setting_item("booking_enable_recaptcha")) {
            $codeCapcha = $request->input('g-recaptcha-response');
            if (!$codeCapcha or !ReCaptchaEngine::verify($codeCapcha)) {
                return $this->sendError(__("Please verify the captcha"));
            }
        }

        $messages = [];
        $rules = [
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'email'           => 'required|string|email|max:255',
            'phone'           => 'required|string|max:255',
            'term_conditions' => 'required',
        ];


        $confirmRegister = $request->input('confirmRegister');
        if (!empty($confirmRegister)) {
            $rules['password'] = 'required|string|confirmed|min:6|max:255';
            $rules['email'] = ['required', 'email', 'max:255', Rule::unique('users')];
            $messages['password.confirmed']   = __('The password confirmation does not match');
            $messages['password.min']   = __('The password must be at least 6 characters');
        }

        $how_to_pay = $request->input('how_to_pay', '');
        $credit = $request->input('credit', 0);
        $payment_gateway = $request->input('payment_gateway');

        // require payment gateway except pay full
        if (empty(floatval($booking->deposit)) || $how_to_pay == 'deposit' || !auth()->check()) {
            $rules['payment_gateway'] = 'required';
        }

        if (auth()->check()) {
            if ($credit > $user->balance) {
                return $this->sendError(__("Your credit balance is :amount", ['amount' => $user->balance]));
            }
        } else {
            // force credit to 0 if not login
            $credit = 0;
        }

        $rules = $service->filterCheckoutValidate($request, $rules);
        if (!empty($rules)) {

            $messages['term_conditions.required']    = __('Term conditions is required field');
            $messages['payment_gateway.required'] = __('Payment gateway is required field');

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return $this->sendError('', ['errors' => $validator->errors()]);
            }
        }

        $wallet_total_used = credit_to_money($credit);
        if ($wallet_total_used > $booking->total) {
            $credit = money_to_credit($booking->total, true);
            $wallet_total_used = $booking->total;
        }

        if ($res = $service->beforeCheckout($request, $booking)) {
            return $res;
        }

        if ($how_to_pay == 'full' and !empty($booking->deposit)) {
            $booking->addMeta('old_deposit', $booking->deposit ?? 0);
        }
        $oldDeposit = $booking->getMeta('old_deposit', 0);
        if (empty(floatval($booking->deposit)) and !empty(floatval($oldDeposit))) {
            $booking->deposit = $oldDeposit;
        }

        // Normal Checkout
        $booking->first_name = $request->input('first_name');
        $booking->last_name = $request->input('last_name');
        $booking->email = $request->input('email');
        $booking->phone = $request->input('phone');
        $booking->address = $request->input('address_line_1');
        $booking->address2 = $request->input('address_line_2');
        $booking->city = $request->input('city');
        $booking->state = $request->input('state');
        $booking->zip_code = $request->input('zip_code');
        $booking->country = "Egypt";
        $booking->customer_notes = $request->input('customer_notes');
        $booking->gateway = $payment_gateway;
        $booking->wallet_credit_used = floatval($credit);
        $booking->wallet_total_used = floatval($wallet_total_used);
        $booking->pay_now = floatval((int)$booking->deposit == null ? $booking->total : (int)$booking->deposit);

        // If using credit
        if ($booking->wallet_total_used > 0) {
            if ($how_to_pay == 'full') {
                $booking->deposit = 0;
                $booking->pay_now = $booking->total;
            } elseif ($how_to_pay == 'deposit') {
                // case guest input credit more than "pay deposit" need to pay
                // Ex : pay deposit 10$ but guest input 20$ -> minus credit balance = 10$
                if ($wallet_total_used > $booking->deposit) {
                    $wallet_total_used = $booking->deposit;
                    $booking->wallet_total_used = floatval($wallet_total_used);
                    $booking->wallet_credit_used = money_to_credit($wallet_total_used, true);
                }
            }

            $booking->pay_now = max(0, $booking->pay_now - $wallet_total_used);
            $booking->paid = $booking->wallet_total_used;
        } else {
            if ($how_to_pay == 'full') {
                $booking->deposit = 0;
                $booking->pay_now = $booking->total;
            }
        }

        $gateways = get_payment_gateways();
        if ($booking->pay_now > 0) {
            $gatewayObj = new $gateways[$payment_gateway]($payment_gateway);
            if (!empty($rules['payment_gateway'])) {
                if (empty($gateways[$payment_gateway]) or !class_exists($gateways[$payment_gateway])) {
                    return $this->sendError(__("Payment gateway not found"));
                }
                if (!$gatewayObj->isAvailable()) {
                    return $this->sendError(__("Payment gateway is not available"));
                }
            }
        }

        if ($booking->wallet_credit_used && auth()->check()) {
            try {
                $transaction = $user->withdraw($booking->wallet_credit_used, [
                    'wallet_total_used' => $booking->wallet_total_used
                ]);
            } catch (\Exception $exception) {
                return $this->sendError($exception->getMessage());
            }

            $transaction->booking_id = $booking->id;
            $transaction->save();
            $booking->wallet_transaction_id = $transaction->id;
        }
        $booking->save();

        //        event(new VendorLogPayment($booking));

        if (Auth::check()) {
            $user = auth()->user();
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->phone = $request->input('phone');
            $user->address = $request->input('address_line_1');
            $user->address2 = $request->input('address_line_2');
            $user->city = $request->input('city');
            $user->state = $request->input('state');
            $user->zip_code = $request->input('zip_code');
            $user->country = $request->input('country');
            $user->save();
        } elseif (!empty($confirmRegister)) {
            $user = new User();
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $user->address = $request->input('address_line_1');
            $user->address2 = $request->input('address_line_2');
            $user->city = $request->input('city');
            $user->state = $request->input('state');
            $user->zip_code = $request->input('zip_code');
            $user->country = $request->input('country');
            $user->password = bcrypt($request->input('password'));
            $user->save();
            event(new Registered($user));
            Auth::loginUsingId($user->id);
            try {
                event(new SendMailUserRegistered($user));
            } catch (\Matrix\Exception $exception) {
                Log::warning("SendMailUserRegistered: " . $exception->getMessage());
            }
            $user->assignRole(setting_item('user_role'));
        }

        $booking->addMeta('locale', app()->getLocale());
        $booking->addMeta('how_to_pay', $how_to_pay);

        // Save Passenger
        $this->savePassengers($booking, $request);

        if ($res = $service->afterCheckout($request, $booking)) {
            return $res;
        }
        $payment_gateway = $request->input("payment_gateway");
        if ($payment_gateway == "myfatoora") {
            $Item = Tour::where('id', $booking->object_id)->first();
            if (app()->getLocale() == 'ar') {
                $ItemName = $Item->title;
            } else {
                $ItemTranslated = TourTranslation::where('origin_id', $Item->id)->where('locale', 'en')->first();
                $ItemName = $ItemTranslated->title;
            }
            $data = [
                "CustomerName" => $booking->first_name . $booking->last_name,
                'InvoiceValue'       => $booking->pay_now,
                'CustomerEmail'   => $booking->email,
                'CustomerReference'  => $booking->id,
                'CallBackUrl'        =>  URL::route('MyFatooraCallback', ['code' => $booking->code]),
                'ErrorUrl'           => URL::route('booking.checkout', [$booking->code]),
                'DisplayCurrency'  => 'EGP',
                'DisplayCurrencyIso' => 'EGP',
                'Language'       => 'en',
                'NotificationOption' => 'Lnk',
            ];
            return $this->fatooraaservice->sendPayment($data);
        } else {
            if ($booking->pay_now > 0) {
                try {
                    $gatewayObj->process($request, $booking, $service);
                } catch (Exception $exception) {
                    return $this->sendError($exception->getMessage());
                }
            } else {
                if ($booking->paid < $booking->total) {
                    $booking->status = $booking::PARTIAL_PAYMENT;
                } else {
                    $booking->status = $booking::PAID;
                }

                if (!empty($booking->coupon_amount) and $booking->coupon_amount > 0 and $booking->total == 0) {
                    $booking->status = $booking::PAID;
                }

                $booking->save();
                event(new BookingCreatedEvent($booking));
                return $this->sendSuccess([
                    'url' => $booking->getDetailUrl()
                ], __("You payment has been processed successfully"));
            }
        }
    }
    protected function savePassengers(Booking $booking, Request $request)
    {
        $booking->passengers()->delete();
        if ($totalPassenger = $booking->calTotalPassenger()) {
            $input = $request->input('passengers', []);
            for ($i = 1; $i <= $totalPassenger; $i++) {
                $passenger = new BookingPassenger();
                $data = [
                    'booking_id' => $booking->id,
                    'first_name' => $input[$i]['first_name'] ?? '',
                    'last_name' => $input[$i]['last_name'] ?? '',
                    'email' => $input[$i]['email'] ?? '',
                    'phone' => $input[$i]['phone'] ?? '',
                ];
                $passenger->fillByAttr(array_keys($data), $data);
                $passenger->save();
            }
        }
    }
    public function confirmPayment(Request $request, $gateway)
    {

        $gateways = get_payment_gateways();
        if (empty($gateways[$gateway]) or !class_exists($gateways[$gateway])) {
            return $this->sendError(__("Payment gateway not found"));
        }
        $gatewayObj = new $gateways[$gateway]($gateway);
        if (!$gatewayObj->isAvailable()) {
            return $this->sendError(__("Payment gateway is not available"));
        }
        return $gatewayObj->confirmPayment($request);
    }

    public function callbackPayment(Request $request, $gateway)
    {
        $gateways = get_payment_gateways();
        if (empty($gateways[$gateway]) or !class_exists($gateways[$gateway])) {
            return $this->sendError(__("Payment gateway not found"));
        }
        $gatewayObj = new $gateways[$gateway]($gateway);
        if (!$gatewayObj->isAvailable()) {
            return $this->sendError(__("Payment gateway is not available"));
        }
        if (!empty($request->input('is_normal'))) {
            return $gatewayObj->callbackNormalPayment();
        }
        return $gatewayObj->callbackPayment($request);
    }

    public function cancelPayment(Request $request, $gateway)
    {

        $gateways = get_payment_gateways();
        if (empty($gateways[$gateway]) or !class_exists($gateways[$gateway])) {
            return $this->sendError(__("Payment gateway not found"));
        }
        $gatewayObj = new $gateways[$gateway]($gateway);
        if (!$gatewayObj->isAvailable()) {
            return $this->sendError(__("Payment gateway is not available"));
        }
        return $gatewayObj->cancelPayment($request);
    }

    public function addToCart(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'service_id'   => 'required|integer',
            'service_type' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError('', ['errors' => $validator->errors()]);
        }
        $service_type = $request->input('service_type');
        $service_id = $request->input('service_id');
        $allServices = get_bookable_services();
        if (empty($allServices[$service_type])) {
            return $this->sendError(__('Service type not found'));
        }
        $module = $allServices[$service_type];
        $service = $module::find($service_id);
        if (empty($service) or !is_subclass_of($service, '\\Modules\\Booking\\Models\\Bookable')) {
            return $this->sendError(__('Service not found'));
        }
        if (!$service->isBookable()) {
            return $this->sendError(__('Service is not bookable'));
        }



        return $service->addToCart($request);
    }



    // public function addToCart(Request $request)
    // {
    //     {

    //         try {
    //             DB::beginTransaction();
    //             $user = User::find($request->userId);
    //             $category = Tour::find($request->tourId);
    //             $booking = new Booking();
    //             $booking->code = md5(uniqid() . rand(0, 99999));
    //             $booking->customer_id = $request->userId;
    //             $booking->object_id = $request->tourId;
    //             $booking->object_model = "tour";
    //             $booking->start_date = $request->selectedDate;
    //             $booking->end_date =  $request->selectedDate;
    //             $booking->total = $request->totalPrice;
    //             $booking->total_guests = $request->countPerson;
    //             $booking->currency = "EGP";
    //             $booking->status = "pending";
    //             $booking->email = $user->email;
    //             $booking->first_name = $user->first_name;
    //             $booking->last_name = $user->last_name;
    //             $booking->phone = $user->phone;
    //             $booking->address = $user->address;
    //             $booking->address2 = $user->address2;
    //             $booking->city = $user->city;
    //             $booking->state = $user->state;
    //             $booking->zip_code = $user->zip_code;
    //             $booking->country = $user->country;
    //             $booking->customer_notes = $request->customer_notes;
    //             $booking->create_user = $request->userId;
    //             $booking->total_before_fees = $request->totalPrice;
    //             $booking->paid = 0.00;
    //             $booking->total_before_discount = $request->totalPrice;
    //             $booking->save();
    //             $booking_date = new Bookning_dates();
    //             $booking_date->tour_id = $request->tourId;
    //             $booking_date->category_id = $category->category_id;
    //             $booking_date->booking_id = $booking->id;
    //             $booking_date->start_from = $request->start_at;
    //             $booking_date->end_at = $request->end_at;
    //             $booking_date->create_user = 1;
    //             $booking_date->active = 0;
    //             $booking_date->day = $request->selectedDate;
    //             $booking_date->save();
    //             DB::table('bravo_booking_meta')->insert([
    //                 'name'       => "person_types",
    //                 'val'        => json_encode($request->personType),
    //                 'booking_id' => $booking->id
    //             ]);

    //             DB::table('bravo_booking_meta')->insert([
    //                 'name'       => "extra_price",
    //                 'val'        => json_encode($request->extra),
    //                 'booking_id' => $booking->id
    //             ]);

    //             DB::table('bravo_booking_meta')->insert([
    //                 'name'       => "base_price",
    //                 'val'        => $booking->total / $booking->total_guests,
    //                 'booking_id' => $booking->id
    //             ]);
    //             DB::table('bravo_booking_meta')->insert([
    //                 'name'       => "menus",
    //                 'val'        => json_encode($request->menus_specialize),
    //                 'booking_id' => $booking->id
    //             ]);
    //             DB::table('bravo_booking_meta')->insert([
    //                 'name'       => "extra_menus",
    //                 'val'        => json_encode($request->extra_menus),
    //                 'booking_id' => $booking->id
    //             ]);
    //             $bookingMeta = DB::table('bravo_booking_meta')->where('booking_id', $booking->id)->where('name', 'extra_price')->first();
    //             if ($bookingMeta->val != null) {
    //                 $extra = json_decode($bookingMeta->val);
    //                 $time = json_decode($extra);
    //                 foreach ($time as $extra_time) {
    //                     if ($extra_time->type == "per_hour") {
    //                         $last_booking_dates = Bookning_dates::where('booking_id', $booking->id)->first();
    //                         $booking_date = new Bookning_dates();
    //                         $booking_date->create_user = 1;
    //                         $booking_date->tour_id = $request->tourId;
    //                         $booking_date->category_id = $category->category_id;
    //                         $booking_date->booking_id = $booking->id;
    //                         $booking_date->start_from = $request->end_at;
    //                         $booking_date->end_at = Carbon::parse($booking_date->start_date)->addHours(2);
    //                         $booking_date->active = 0;
    //                         $booking_date->day = $request->selectedDate;
    //                         $booking_date->save();
    //                     }
    //                 }
    //             }
    //             $deposit = Settings::where('name', 'tour_deposit_enable')->first();
    //             if ($deposit->val == 1) {
    //                 $type = Settings::where('name', 'tour_deposit_type')->first();
    //                 $amount = Settings::where('name', 'tour_deposit_amount')->first();
    //                 $type = $type->val;
    //                 $amount = $amount->val;
    //             }

    //             if ($category->enable_service_fee == 1) {
    //                 $service_fee = $category->service_fee;
    //             } else {
    //                 $service_fee = [];
    //             }
    //             DB::commit();
    //             return response()->json([
    //                 "message" => true,
    //                 "booking_id" => $booking->id,
    //                 "enable_cash" => 1,
    //                 'success' => true,
    //                 'service_fee' => $service_fee,
    //                 'deposit' => [
    //                     'enabled' => $deposit->val,
    //                     'type' => $type ?? null,
    //                     'amount' => $amount ?? null
    //                 ],
    //             ]);
    //         } catch (\Exception $e) {
    //             DB::rollback();
    //             return response()->json([
    //                 "message" => false,
    //                 "error_message" => $e->getMessage(),
    //                 'success' => false

    //             ]);
    //         }
    //     }
    // }
    public function getGateways()
    {

        $all = get_payment_gateways();
        $res = [];
        foreach ($all as $k => $item) {
            if (class_exists($item)) {
                $obj = new $item($k);
                if ($obj->isAvailable()) {
                    $res[$k] = $obj;
                }
            }
        }
        return $res;
    }

    public function detail(Request $request, $code)
    {
        if (!is_enable_guest_checkout() and !Auth::check()) {
            return $this->sendError(__("You have to login in to do this"))->setStatusCode(401);
        }

        $booking = $this->booking::where('code', $code)->first();
        if (empty($booking)) {
            abort(404);
        }

        if ($booking->status == 'draft') {
            return redirect($booking->getCheckoutUrl());
        }
        if (!is_enable_guest_checkout() and $booking->customer_id != Auth::id()) {
            abort(404);
        }
        $menus = $booking->getJsonMeta('menus');
        $selectedItems = [];
        if ($menus) {

            foreach ($menus as $menu) {
                if ($menu['itemId'] == $menu['submenuId']) {
                    $item = MenuTour::find($menu['submenuId']);
                    $item->count = $menu['count'];
                    $item_en = MenusTranslation::where('origin_id', $item->id)->where('locale', 'en')->first();
                    $item->name_en = $item_en->name;
                } else {
                    $item = Terms::where('id', $menu['itemId'])->first();
                    $item->count = $menu['count'];
                    $item_en = TermsTranslation::where('origin_id', $item->id)->where('locale', 'en')->first();
                    $item->name_en = $item_en->name;
                }

                $selectedItems[] = $item;
            }
        }
        $data = [
            'page_title' => __('Booking Details'),
            'booking'    => $booking,
            'service'    => $booking->service,
            'selectedItems' => $selectedItems,
        ];

        if ($booking->gateway) {
            $data['gateway'] = get_payment_gateway_obj($booking->gateway);
        }
        return view('Booking::frontend/detail', $data);
    }

    public function exportIcal($type, $id = false)
    {
        if (empty($type) or empty($id)) {
            return $this->sendError(__('Service not found'));
        }

        $allServices = get_bookable_services();
        $allServices['room'] = 'Modules\Hotel\Models\HotelRoom';
        if (empty($allServices[$type])) {
            return $this->sendError(__('Service type not found'));
        }
        $module = $allServices[$type];

        $path = '/ical/';
        $fileName = 'booking_' . $type . '_' . $id . '.ics';
        $fullPath = $path . $fileName;

        $content  = $this->booking::getContentCalendarIcal($type, $id, $module);
        Storage::disk('uploads')->put($fullPath, $content);
        $file = Storage::disk('uploads')->get($fullPath);

        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');

        echo $file;
    }

    public function addEnquiry(Request $request)
    {
        $rules =  [
            'service_id'   => 'required|integer',
            'service_type' => 'required',
            'enquiry_name' => 'required',
            'enquiry_note' => 'required',
            'enquiry_email' => [
                'required',
                'email',
                'max:255',
            ],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendError('', ['errors' => $validator->errors()]);
        }

        if (setting_item('booking_enquiry_enable_recaptcha')) {
            $codeCapcha = trim($request->input('g-recaptcha-response'));
            if (empty($codeCapcha) or !ReCaptchaEngine::verify($codeCapcha)) {
                return $this->sendError(__("Please verify the captcha"));
            }
        }

        $service_type = $request->input('service_type');
        $service_id = $request->input('service_id');
        $allServices = get_bookable_services();
        if (empty($allServices[$service_type])) {
            return $this->sendError(__('Service type not found'));
        }
        $module = $allServices[$service_type];
        $service = $module::find($service_id);
        if (empty($service) or !is_subclass_of($service, '\\Modules\\Booking\\Models\\Bookable')) {
            return $this->sendError(__('Service not found'));
        }
        $row = new $this->enquiryClass();
        $row->fill([
            'name' => $request->input('enquiry_name'),
            'email' => $request->input('enquiry_email'),
            'phone' => $request->input('enquiry_phone'),
            'note' => $request->input('enquiry_note'),
        ]);
        $row->object_id = $request->input("service_id");
        $row->object_model = $request->input("service_type");
        $row->status = "pending";
        $row->vendor_id = $service->author_id;
        $row->save();
        event(new EnquirySendEvent($row));
        return $this->sendSuccess([
            'message' => __("Thank you for contacting us! We will be in contact shortly.")
        ]);
    }

    public function setPaidAmount(Request $request)
    {
        $rules =  [
            'remain'   => 'required|integer',
            'id' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendError('', ['errors' => $validator->errors()]);
        }

        $id = $request->input('id');
        $remain = floatval($request->input('remain'));
        if ($remain < 0) {
            return $this->sendError(__('Remain can not smaller than 0'));
        }

        $booking = Booking::where('id', $id)->first();
        if (empty($booking)) {
            return $this->sendError(__('Booking not found'));
        }
        $booking->pay_now = $remain;
        $booking->paid = floatval($booking->total) - $remain;
        event(new SetPaidAmountEvent($booking));
        if ($remain == 0) {
            $booking->status = $booking::PAID;
            $booking->sendStatusUpdatedEmails();
            event(new BookingUpdatedEvent($booking));
        }

        $booking->save();

        return $this->sendSuccess([
            'message' => __("You booking has been changed successfully")
        ]);
    }

    public function modal(Booking $booking)
    {
        if (!is_admin() and $booking->vendor_id != auth()->id() and $booking->customer_id != auth()->id()) abort(404);
        $menus = $booking->getJsonMeta('menus');
        if ($booking->is_app == 1) {
            $selectedItems = [];
            if ($menus) {
                foreach ($menus as $menu) {
                    if ($menu['itemId'] == $menu['submenuId']) {
                        $item = MenuTour::find($menu['submenuId']);
                        $item->count = $menu['count'];
                        $item_en = MenusTranslation::where('origin_id', $item->id)->where('locale', 'en')->first();
                        $item->name_en = $item_en->name;
                    } else {
                        $item = Terms::where('id', $menu['itemId'])->first();
                        $item->count = $menu['count'];
                        $item_en = TermsTranslation::where('origin_id', $item->id)->where('locale', 'en')->first();
                        $item->name_en = $item_en->name;
                    }
                    $selectedItems[] = $item;
                }
            }
        } else {
            $menus = json_decode($menus, true);

            $selectedItems = [];
            if ($menus) {
                foreach ($menus as $menu) {
                    if ($menu['itemId'] == 0) {
                        $item = MenuTour::find($menu['submenuId']);
                        $item->count = $menu['count'];
                        $item_en = MenusTranslation::where('origin_id', $item->id)->where('locale', 'en')->first();
                        $item->name_en = $item_en->name;
                    } else {
                        $item = Terms::where('id', $menu['itemId'])->first();
                        $item->count = $menu['count'];
                        $item_en = TermsTranslation::where('origin_id', $item->id)->where('locale', 'en')->first();
                        $item->name_en = $item_en->name;
                    }
                    $selectedItems[] = $item;
                }
            }
        }
        
        
        return view('Booking::frontend.detail.modal', ['booking' => $booking, 'service' => $booking->service, 'selectedItems' => $selectedItems]);
    }

    public function MyFatooraCallback(Request $request, $code)
    {
        $data = [];
        $data['Key'] = $request->paymentId;
        $data['KeyType'] = 'paymentId';


        return $this->fatooraaservice->getPaymentStatus($data, $code);
    }
}
