<?php

namespace Modules\User\Controllers;

use DateInterval;
use DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Matrix\Exception;
use Modules\Boat\Models\Boat;
use Modules\Booking\Models\Service;
use Modules\Car\Models\Car;
use Modules\Event\Models\Event;
use Modules\Flight\Models\Flight;
use Modules\FrontendController;
use Modules\Hotel\Models\Hotel;
use Modules\Space\Models\Space;
use Modules\Tour\Models\Tour;
use Modules\User\Events\NewVendorRegistered;
use Modules\User\Events\UserSubscriberSubmit;
use Modules\User\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Vendor\Models\VendorRequest;
use Validator;
use Modules\Media\Models\MediaFile;
use Modules\User\Models\User;
use Carbon\Carbon;

use Modules\Core\Models\ExtrasTranslation;

use Modules\Tour\Models\MenuExtras;
use Modules\Tour\Models\TourCategory;

use Modules\Booking\Models\Booking;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use Modules\Booking\Models\Enquiry;
use Illuminate\Support\Str;
use Modules\Core\Models\MenusTranslation;
use Modules\Core\Models\MenuTour;
use Modules\Core\Models\Settings;
use Modules\Core\Models\Terms;
use Modules\Core\Models\TermsTranslation;
use Modules\Coupon\Models\CouponBookings;
use Modules\Tour\Models\Bookning_dates;
use Modules\Tour\Models\TourMeta;

class UserController extends FrontendController
{
    use AuthenticatesUsers;

    protected $enquiryClass;

    public function __construct()
    {
        $this->enquiryClass = Enquiry::class;
        parent::__construct();
    }

    public function dashboard(Request $request)
    {
        $this->checkPermission('dashboard_vendor_access');
        $user_id = Auth::id();
        $data = [
            'cards_report'       => Booking::getTopCardsReportForVendor($user_id),
            'earning_chart_data' => Booking::getEarningChartDataForVendor(strtotime('monday this week'), time(), $user_id),
            'page_title'         => __("Vendor Dashboard"),
            'breadcrumbs'        => [
                [
                    'name'  => __('Dashboard'),
                    'class' => 'active'
                ]
            ]
        ];
        return view('User::frontend.dashboard', $data);
    }

    public function reloadChart(Request $request)
    {
        $chart = $request->input('chart');
        $user_id = Auth::id();
        switch ($chart) {
            case "earning":
                $from = $request->input('from');
                $to = $request->input('to');
                return $this->sendSuccess([
                    'data' => Booking::getEarningChartDataForVendor(strtotime($from), strtotime($to), $user_id)
                ]);
                break;
        }
    }

    public function profile(Request $request)
    {
        $user = Auth::user();
        $data = [
            'dataUser'         => $user,
            'page_title'       => __("Profile"),
            'breadcrumbs'      => [
                [
                    'name'  => __('Setting'),
                    'class' => 'active'
                ]
            ],
            'is_vendor_access' => $this->hasPermission('dashboard_vendor_access')
        ];
        return view('User::frontend.profile', $data);
    }

    public function profileUpdate(Request $request)
    {
        if (is_demo_mode()) {
            return back()->with('error', "Demo mode: disabled");
        }
        $user = Auth::user();
        $messages = [
            'user_name.required'      => __('The User name field is required.'),
        ];
        $request->validate([
            'first_name' => 'required|max:255',
            'last_name'  => 'required|max:255',
            'email'      => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'user_name' => [
                'required',
                'max:255',
                'min:4',
                'string',
                'alpha_dash',
                Rule::unique('users')->ignore($user->id)
            ],
            'phone'       => [
                'required',
                Rule::unique('users')->ignore($user->id)
            ],
        ], $messages);
        $input = $request->except('bio');
        $user->fill($input);
        $user->bio = clean($request->input('bio'));
        $user->birthday = date("Y-m-d", strtotime($user->birthday));
        $user->user_name = Str::slug($request->input('user_name'), "_");
        $user->save();
        return redirect()->back()->with('success', __('Update successfully'));
    }

    public function bookingHistory(Request $request)
    {
        $user_id = Auth::id();
        $user = User::find($user_id);
        if ($user->role_id == 1 || $user->role_id == 2) {
            $booking = Booking::getBookingHistory($request->input('status'), false, $user_id,false);
        } else {
            $booking = Booking::getBookingHistory($request->input('status'), $user_id);
        }
        
        $categories=TourCategory::with('translation')->get();
        $data = [
            'bookings'    => $booking,
            'categories'    => $categories,
            'statues'     => config('booking.statuses'),
            'breadcrumbs' => [
                [
                    'name'  => __('Booking History'),
                    'class' => 'active'
                ]
            ],
            'page_title'  => __("Booking History"),
        ];
        return view('User::frontend.bookingHistory', $data);
    }
    
    
    public function filterbooking(Request $request)
    {
        $user_id = Auth::id();
        $user = User::find($user_id);
        $categories=TourCategory::with('translation')->get(); 
        $filteredBookings = collect(); 
        if ($user->role_id == 1 || $user->role_id == 2) {
            $bookingQuery = Booking::where('vendor_id', $user_id)->orWhere('customer_id', $user_id);
            if ($request->date_filter!=null) {
                $user_id = Auth::id();
                $user = User::find($user_id);
                $bookingQuery = Booking::where('vendor_id', $user_id)->orWhere('customer_id', $user_id);
                $date_filter = $request->date_filter;
                $user_timezone = $user->timezone;
                $start_date = Carbon::createFromFormat('Y-m-d', $date_filter, $user_timezone)
                    ->setTimezone(config('app.timezone'))
                    ->startOfDay();
                $end_date = $start_date->copy()->endOfDay();
                $bookings = $bookingQuery->get();
                $filteredBookings = $bookings->filter(function ($booking) use ($start_date, $end_date, $user_timezone) {
                    $start_date_user_timezone = Carbon::parse($booking->start_date, 'UTC')->setTimezone($user_timezone)
                        ->startOfDay();
                    return $start_date_user_timezone >= $start_date && $start_date_user_timezone <= $end_date;
                });
            }
            
            
            if($request->category_id !==null){
              $filteredBookings=  $filteredBookings->where('category_id',$request->category_id);
            }
            if($request->category_id !==null  && $request->date_filter ==null){
                $filteredBookings = Booking::where('category_id',$request->category_id)->orderBy('id','desc');
            }
            
        
            // $filteredBookings->paginate(1);
            $data = [
                'bookings'    => $filteredBookings,
                'categories'    => $categories,
                'statues'     => config('booking.statuses'),
                'breadcrumbs' => [
                    [
                        'name'  => __('Booking History'),
                        'class' => 'active'
                    ]
                ],
                'page_title'  => __("Booking History"),
            ];
        return view('User::frontend.Filterbooking', $data);
        }
    }
    
//     public function filterbooking(Request $request)
//     {
//     $user_id = Auth::id();
//     $user = User::find($user_id);
//     $categories = TourCategory::with('translation')->get(); 
//     $filteredBookingsQuery = Booking::where(function ($query) use ($user_id) {
//         $query->where('vendor_id', $user_id)->orWhere('customer_id', $user_id);
//     });

//     if ($user->role_id == 1 || $user->role_id == 2) {
//         $bookingQuery = Booking::where('vendor_id', $user_id)->orWhere('customer_id', $user_id);

//         if ($request->date_filter != null) {
//             $date_filter = $request->date_filter;
//             $user_timezone = $user->timezone;
//             $start_date = Carbon::createFromFormat('Y-m-d', $date_filter, $user_timezone)
//                 ->setTimezone(config('app.timezone'))
//                 ->startOfDay();
//             $end_date = $start_date->copy()->endOfDay();
//             $bookings = $bookingQuery->get();

//             $filteredBookings = $bookings->filter(function ($booking) use ($start_date, $end_date, $user_timezone) {
//                 $start_date_user_timezone = Carbon::parse($booking->start_date, 'UTC')->setTimezone($user_timezone)
//                     ->startOfDay();
//                 return $start_date_user_timezone >= $start_date && $start_date_user_timezone <= $end_date;
//             });
//         }
//     }

//     if ($request->category_id !== null) {
//         $filteredBookingsQuery->where('category_id', $request->category_id);
//     }

//     if ($request->category_id !== null && $request->date_filter === null) {
//         $filteredBookingsQuery->where('category_id', $request->category_id);
//     }

//     $filteredBookings = $filteredBookingsQuery->paginate(1);

//     $data = [
//         'bookings'    => $filteredBookings,
//         'categories'  => $categories,
//         'statuses'    => config('booking.statuses'),
//         'breadcrumbs' => [
//             [
//                 'name'  => __('Booking History'),
//                 'class' => 'active'
//             ]
//         ],
//         'page_title' => __("Booking History"),
//     ];

//     return view('User::frontend.Filterbooking', $data);
// }

    
    
    public function cancel_booking(Request $request, $id)
    {
        $booking = Booking::find($id);
        $minimum = Settings::find(489);
        $startDate = new DateTime($booking->start_date);
        $modifiedDate = $startDate->sub(new DateInterval("P{$minimum->val}D"));
        $modifiedDateString = $modifiedDate->format('Y-m-d');
        $currentDate = new DateTime();
        if ($modifiedDate  >= $currentDate) {
            $booking->status = "cancelled";
            $booking->is_paid = 0;
            $booking->save();
            $booking_dates = Bookning_dates::where('booking_id', $booking->id)->get();
            foreach ($booking_dates as $booking_dates) {
                $booking_dates->active = 0;
                $booking_dates->save();
            }
            return response()->json([
                "message" => "Booking Cancelled Successfully Please Contact The Administration To Refund Your Money ",
                "status" => true,
                'success' => true
            ]);
        } else {
            if ($request->refunded == "1") {
                $booking->status = "cancelled";
                $booking->is_paid = 0;
                $booking->save();
                $booking_dates = Bookning_dates::where('booking_id', $booking->id)->get();
                foreach ($booking_dates as $booking_dates) {
                    $booking_dates->active = 0;
                    $booking_dates->save();
                }
                return response()->json([
                    "message" => "Booking Cancelled Successfully",
                    "status" => true,
                    'success' => true

                ]);
            }
            return response()->json([
                "message" => "You Will Miss Your Deposit",
                "status" => false,
                'success' => false

            ]);
        }
    }
    public function edit_booking(Request $request, $id)
    {
        $row = Booking::find($id);
        $booking_dates=Bookning_dates::where('booking_id',$id)->get();
        $extra_price = TourMeta::where('tour_id', $row->object_id)->get();
        $extra_price = $extra_price[0]["extra_price"];
        $choosed_extra_price = DB::table('bravo_booking_meta')->where('booking_id', $id)->where('name', 'extra_price')->first();
        $choosed_extra_price = $choosed_extra_price->val;
        $choosed_menus = DB::table('bravo_booking_meta')->where('booking_id', $id)->where('name', 'menus')->first();
        $choosed_menus_array = json_decode($choosed_menus->val, true);
        $tour = Tour::find($row->object_id);
        $menuIds = json_decode($tour->menu_id, true);
        if ($menuIds == null) {
            $all_menus = [];
            $extra_terms = [];
            $jsoned_choosed = "[]";

            $menusWithChildren = [];
            $extra_terms_last = [];
        } else {
            $menuIds = array_map('intval', $menuIds);
            $categories = MenuTour::whereIn('id', $menuIds)->get();
            $menusWithChildren = [];
            foreach ($categories as $category) {
                $category->menus = MenuTour::where('parent_id', $category->id)->get();
                $category_en = MenusTranslation::where('origin_id', $category->id)->where('locale', 'en')->first();
                $category->name_en = $category_en->name ?? '';
                $category->description_en = $category->description ?? '';
                foreach ($category->menus as $menu) {
                    $menu_en = MenusTranslation::where('origin_id', $menu->id)->where('locale', 'en')->first();
                    $menu->name_en = $menu_en->name;
                    $menu->description_en = $menu_en->description;
                    $menu->terms = Terms::where('menu_id', $menu->id)->get();
                    foreach ($menu->terms as $term) {

                        $term_en = TermsTranslation::where('origin_id', $term->id)->where('locale', 'en')->first();
                        $term->name_en = $term_en->name;
                        $term->description_en = $term_en->description;
                        $mediaFile = MediaFile::find($term->image_id);
                        if ($mediaFile) {
                            $term->image_path = "uploads/" . $mediaFile->file_path;
                        }
                    }
                    $menu->extra_terms = MenuExtras::where('menu_id', $menu->id)->get();
                    foreach ($menu->extra_terms as $extra_terms) {
                        $extra_terms_en = ExtrasTranslation::where('origin_id', $extra_terms->id)->where('locale', 'en')->first();
                        $extra_terms->name_en = $extra_terms_en->name;
                    }
                }
                $menusWithChildren[] = $category;
            }
            $menu_tour = MenuTour::whereIn('parent_id', $menuIds)->get();
            $ids = $menu_tour->pluck('id');
            $extra_terms = MenuExtras::whereIn('menu_id', $ids)->get();
            $extra_terms_last = [];
            foreach ($extra_terms as $extra_terms) {
                $extra_terms_en = ExtrasTranslation::where('origin_id', $extra_terms->id)->where('locale', 'en')->first();
                $extra_terms->name_en = $extra_terms_en->name;
                $extra_terms_last[] = $extra_terms;
            }
            $jsoned_choosed = json_encode($choosed_menus_array);
        }

        $data = [
            'row'               => $row,
            'extra_price'               => $extra_price,
            'choosed_extra_price'               => $choosed_extra_price,
            'choosed_menus_array'               => $choosed_menus_array,
            'booking_dates'               => $booking_dates,
            'jsoned_choosed'               => $jsoned_choosed,
            'menusWithChildren' => $menusWithChildren,
            'extra_terms' => $extra_terms_last,
            'enable_multi_lang' => false,
            'breadcrumbs'       => [
                [
                    'name' => __('Your Booking'),
                    'url'  => route('tour.admin.index')
                ],
                [
                    'name'  => __('Edit Booking'),
                    'class' => 'active'
                ],
            ],
            'page_title' => __('Edit Booking')
        ];


        if ($row->is_app == 0) {
            return view('User::frontend.EditBookingForApp', $data);
        } else {
            return view('User::frontend.EditBooking', $data);
        }
    }

    public function update_booking(Request $request)
    {
        

        
        if($request->extra_price == null)
        {
            $additional_price=0;
        }else{
            $additional_price=$request->extra_price;

        }
        $booking = Booking::find($request->booking_id);
        $extra_price_strings = $request->extra_price ?? [];
        $extra_prices = [];
        $extra_price = TourMeta::where('tour_id', $request->tour_id)->get();
        $extra_price = $extra_price[0]["extra_price"];
        foreach ($extra_price_strings as $name) {
            $item = collect($extra_price)->firstWhere('name', $name);
            if ($item) {
                $extra_prices[] = (object)[
                    'name' => $item['name'],
                    'name_en' => $item['name_en'],
                    'price' => $item['price'],
                    'type' => $item['type'],
                    'total' => $item['price']
                ];
            }
        }
        $extra_price =  DB::table('bravo_booking_meta')
            ->where('booking_id', $request->booking_id)
            ->where('name', 'extra_price')
            ->update(['val' => $extra_prices]);
        $extra_price =  DB::table('bravo_booking_meta')
            ->where('booking_id', $request->booking_id)
            ->where('name', 'extra_price')->first();

        $extras = json_decode($extra_price->val, true);
        $extra_total = 0;
        foreach ($extras as $extra) {
            $extra_total += (int)$extra['total'];
        }
        $selectedMenusInput = $request->input('selected-menus-input');


        if ($selectedMenusInput == null) {
            $selectedMenusInput = [];
        }
        $filteredArray = array_filter($selectedMenusInput, function ($value) {
            return $value !== null;
        });
        if (!empty($filteredArray)) {
            $input = $request->input('selected-menus-input');
            $data = array_map(function ($jsonString) {
                return json_decode($jsonString, true);
            }, $input);
            $data = array_filter($data, function ($item) {
                return $item !== null;
            });
            $mergedData = [];
            foreach ($data as $item) {
                foreach ($item as $items) {
                    $compositeKey = $items['menuId'] . '-' . $items['submenuId'] . '-' . $items['itemId'];
                    if (isset($mergedData[$compositeKey])) {
                        $mergedData[$compositeKey]['count'] += (int)$items['count'];
                    } else {
                        $mergedData[$compositeKey] = $items;
                    }
                }
            }
            $mergedArray = array_values($mergedData);
            $output = json_encode($mergedArray);
            $menus =  DB::table('bravo_booking_meta')
                ->where('booking_id', $request->booking_id)
                ->where('name', 'menus')
                ->update(['val' => $output]);
            $updated_menus = DB::table('bravo_booking_meta')
                ->where('booking_id', $request->booking_id)
                ->where('name', 'menus')->first();
            $menuItems = json_decode($updated_menus->val, true);
            $menus_total = 0;
            foreach ($menuItems as $menuItem) {
                if ($menuItem['submenuId'] == $menuItem['itemId']) {
                    $submenuId = $menuItem['submenuId'];
                    $menu = MenuTour::find($submenuId);
                    $price = $menu->price * $menuItem['count'];
                } else {
                    if ($menuItem['itemId'] == 0) {
                        $submenuId = $menuItem['submenuId'];
                        $menu = MenuTour::find($submenuId);
                        $price = $menu->price * $menuItem['count'];

                    } else {
                        $itemId = $menuItem['itemId'];
                        $menu = Terms::find($itemId);
                        $price = $menu->price * $menuItem['count'];
                    }
                }

                $menus_total += $price;
            }
        } else {
            $menus =  DB::table('bravo_booking_meta')
                ->where('booking_id', $request->booking_id)
                ->where('name', 'menus')->first();

            $menuItems = json_decode($menus->val, true);

            $menus_total = 0;

            if (!is_null($menuItems) && is_array($menuItems)) {
                foreach ($menuItems as $menuItem) {

                    if ($menuItem['submenuId'] == $menuItem['itemId']) {
                        $submenuId = $menuItem['submenuId'];
                        $menu = MenuTour::find($submenuId);
                        $price = $menu->price * $menuItem['count'];
                    } else {
                        $itemId = $menuItem['itemId'];
                        $menu = Terms::find($itemId);
                        $price = $menu->price * $menuItem['count'];
                    }
                    $menus_total += $price;
                }
            }
        }
        if ($request->total_guests == $booking->total_guests) {
            $person_type = DB::table('bravo_booking_meta')
                ->where('booking_id', $request->booking_id)
                ->where('name', 'person_types')->first();
            $person_data = json_decode($person_type->val, true);
            if (isset($person_data[0]['display_price'])) {
                $display_price = floatval($person_data[0]['display_price']);
            } else {
                $display_price = isset($person_data[0]['price']) ? floatval($person_data[0]['price']) : 0;
            }
            $number = isset($person_data[0]['number']) ? intval($person_data[0]['number']) : 0;
            $person_total = $display_price * $number;
        } else {
            $person_type = DB::table('bravo_booking_meta')->where('booking_id', $request->booking_id)->where('name', 'person_types')->first();
            $person_types_array = json_decode($person_type->val, true);
            $newNumber = $request->total_guests;
            $person_types_array[0]['number'] = $newNumber;
            $updated_person_types_json = json_encode($person_types_array);
            $person_type = DB::table('bravo_booking_meta')
                ->where('booking_id', $request->booking_id)
                ->where('name', 'person_types')
                ->update(['val' => $updated_person_types_json]);
            $person_type = DB::table('bravo_booking_meta')
                ->where('booking_id', $request->booking_id)
                ->where('name', 'person_types')->first();
            $person_data = json_decode($person_type->val, true);

            if ($booking->is_app == 0) {
                $display_price = isset($person_data[0]['price']) ? floatval($person_data[0]['price']) : 0;
            } else {
                $display_price = isset($person_data[0]['display_price']) ? floatval($person_data[0]['display_price']) : 0;
            }
            

            
            $number = isset($person_data[0]['number']) ? intval($person_data[0]['number']) : 0;
            $person_total = $display_price * $number;
        }
        $tour_total = $booking->service->price;
        $custom_price=$request->custom_price;
        $total_after_update = $person_total + $extra_total + $menus_total + $tour_total +$custom_price;
        $coupon_date = CouponBookings::where('booking_id', $booking->id)->first();
        if ($coupon_date) {
            if ($coupon_date->coupon_data['discount_type'] == "percent") {
                $coupon_date->coupon_amount = ($total_after_update / 100)  *  $coupon_date->coupon_data['amount'];
                $coupon_date->save();
            } else {
                $coupon_date->coupon_amount = $coupon_date->coupon_data['amount'];
                $coupon_date->save();
            }
            $booking->total_before_discount = $total_after_update;
            $booking->total = $total_after_update - $coupon_date->coupon_amount;
            $booking->total_before_fees = $total_after_update - $coupon_date->coupon_amount;
            $booking->first_name = $request->first_name;
            $booking->last_name = $request->last_name;
            $booking->email = $request->email;
            $booking->phone = $request->phone;
            $booking->total_guests = $request->total_guests;
            $booking->coupon_amount = $coupon_date->coupon_amount;
            $booking->is_app= 1;
            $booking->save();
            if ($booking->service->enable_service_fee == 1) {
                $service_fee = $booking->service->service_fee[0]['price'];
                $total_before_fees = $total_after_update - $coupon_date->coupon_amount;
                $service_total = ($total_before_fees * $service_fee) / 100;
                $booking->total_before_fees  = $total_before_fees;
                $booking->total = $total_before_fees +  $service_total;
                $booking->vendor_service_fee_amount = $service_total;
                $booking->is_app= 1;
                $booking->save();
            }
        } else {
            if ($booking->service->enable_service_fee == 1) {
                $service_fee = $booking->service->service_fee[0]['price'];
                $service_total = ($total_after_update * $service_fee) / 100;
                $booking->total_before_fees  = $total_after_update;
                $booking->total = $total_after_update +  $service_total;
                $booking->vendor_service_fee_amount = $service_total;
                $booking->is_app= 1;

                $booking->save();
            } else {
                $booking->total = $total_after_update;
                $booking->total_before_fees = $total_after_update;
                $booking->coupon_amount = 0.00;
                $booking->total_before_discount = $total_after_update;
                $booking->is_app= 1;

                $booking->save();
            }
        }
        return redirect()->back();
    }
    public function subscribe(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|max:255'
        ]);
        $check = Subscriber::withTrashed()->where('email', $request->input('email'))->first();
        if ($check) {
            if ($check->trashed()) {
                $check->restore();
                return $this->sendSuccess([], __('Thank you for subscribing'));
            }
            return $this->sendError(__('You are already subscribed'));
        } else {
            $a = new Subscriber();
            $a->email = $request->input('email');
            $a->first_name = $request->input('first_name');
            $a->last_name = $request->input('last_name');
            $a->save();

            event(new UserSubscriberSubmit($a));
            return $this->sendSuccess([], __('Thank you for subscribing'));
        }
    }
    public function upgradeVendor(Request $request)
    {
        $user = Auth::user();
        $vendorRequest = VendorRequest::query()->where("user_id", $user->id)->where("status", "pending")->first();
        if (!empty($vendorRequest)) {
            return redirect()->back()->with('warning', __("You have just done the become vendor request, please wait for the Admin's approved"));
        }
        // check vendor auto approved
        $vendorAutoApproved = setting_item('vendor_auto_approved');
        $dataVendor['role_request'] = setting_item('vendor_role');
        if ($vendorAutoApproved) {
            if ($dataVendor['role_request']) {
                $user->assignRole($dataVendor['role_request']);
            }
            $dataVendor['status'] = 'approved';
            $dataVendor['approved_time'] = now();
        } else {
            $dataVendor['status'] = 'pending';
        }
        $vendorRequestData = $user->vendorRequest()->save(new VendorRequest($dataVendor));
        try {
            event(new NewVendorRegistered($user, $vendorRequestData));
        } catch (Exception $exception) {
            Log::warning("NewVendorRegistered: " . $exception->getMessage());
        }
        return redirect()->back()->with('success', __('Request vendor success!'));
    }
    public function permanentlyDelete(Request $request)
    {
        if (is_demo_mode()) {
            return back()->with('error', "Demo mode: disabled");
        }
        if (!empty(setting_item('user_enable_permanently_delete'))) {
            $user = Auth::user();
            \DB::beginTransaction();
            try {
                Service::where('author_id', $user->id)->delete();
                Tour::where('author_id', $user->id)->delete();
                Car::where('author_id', $user->id)->delete();
                Space::where('author_id', $user->id)->delete();
                Hotel::where('author_id', $user->id)->delete();
                Event::where('author_id', $user->id)->delete();
                Boat::where('author_id', $user->id)->delete();
                Flight::where('author_id', $user->id)->delete();
                $user->sendEmailPermanentlyDelete();
                $user->delete();
                \DB::commit();
                Auth::logout();
                if (is_api()) {
                    return $this->sendSuccess([], 'Deleted');
                }
                return redirect(route('home'));
            } catch (\Exception $exception) {
                \DB::rollBack();
            }
        }
        if (is_api()) {
            return $this->sendError('Error. You can\'t permanently delete');
        }
        return back()->with('error', __('Error. You can\'t permanently delete'));
    }
}
