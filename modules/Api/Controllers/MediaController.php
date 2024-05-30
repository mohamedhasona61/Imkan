<?php

namespace Modules\Api\Controllers;

use App\Http\Controllers\Controller;
use App\User as User;
use Modules\Location\Models\Location;
use Modules\Tour\Models\TourCategory;
use Modules\Media\Models\MediaFile;
use Modules\Tour\Models\Tour;
use Illuminate\Http\Request;
use Modules\User\Models\UserWishList;
use Modules\Coupon\Models\Coupon;
use Modules\Tour\Models\CategoryTimeSlot;
use Modules\Core\Models\MenuTour;
use Modules\Core\Models\Settings;
use Modules\Core\Models\Terms;
use Modules\Review\Models\Review;
use Modules\Review\Models\ReviewMeta;
use Modules\Tour\Models\TourMeta;
use Modules\Tour\Models\Bookning_dates;
use Modules\Tour\Models\MenuExtras;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Booking\Models\Booking;
use Modules\Coupon\Models\CouponServices;
use Modules\Coupon\Models\CouponBookings;
use Modules\Tour\Models\TourTranslation;
use Modules\Tour\Models\TourCategoryTranslation;
use Modules\Location\Models\LocationTranslation;
use Modules\Core\Models\MenusTranslation;
use Modules\Core\Models\TermsTranslation;
use Modules\Core\Models\ExtrasTranslation;
use Illuminate\Support\Str;
use App\Helpers\FireBaseNotifications;

use DateInterval;
use DateTime;

class MediaController extends Controller
{
    public function category()
    {
        $category = TourCategory::get();
        $categoryIds = $category->pluck('id');
        $categories = [];
        foreach ($category as $category) {
            $translation = TourCategoryTranslation::where('origin_id', $category->id)->where('locale', 'en')->first();
            $image = MediaFile::find($category->banner_image_id);
            if ($image) {
                $image = "uploads/" . $image->file_path;
            } else {
                $image = null;
            }
            $categories[] = [
                "id" => $category->id,
                "name" =>  $category->name,
                "name_en" =>  $translation->name,
                "content" => $category->content,
                "content_en" => $translation->content,
                "video" => $category->video,
                "banner_image_id" => $image,
                "slug" =>  $category->slug,
                "status" =>  $category->status,
                "_lft" =>  $category->_lft,
                "_rgt" =>  $category->rgt,
                "parent_id" =>  $category->parent_id,
                "create_user" =>  $category->create_user,
                "update_user" =>  $category->update_user,
                "deleted_at" => $category->deleted_at,
                "origin_id" => $category->origin_id,
                "lang" =>  $category->lang,
                "created_at" =>  $category->created_at,
                "updated_at" => $category->updated_at,
            ];
        }


        return response()->json([
            'category' => $categories,
            'message' => 'Success',
            'status' => 1,
            'success' => true
        ]);
    }
    public function location()
    {
        $locations = Location::get();
        $locationIds = $locations->pluck('id');

        $locationData = [];

        foreach ($locations as $location) {
            $translation = LocationTranslation::where('origin_id', $location->id)->where('locale', 'en')->first();
            $image = null;
            if ($location->banner_image_id != null) {
                $image = MediaFile::find($location->banner_image_id);
                if ($image) {
                    $image = "uploads/" . $image->file_path;
                }
            }

            $locationData[] = [
                'id' => $location->id,
                'name' => $location->name,
                'name_en' => $translation->name,
                'content' => $location->content,
                'content_en' => $translation->content,
                'slug' => $location->slug,
                'image_id' => $location->image_id,
                'map_lat' => $location->map_lat,
                'map_lng' => $location->map_lng,
                'map_zoom' => $location->map_zoom,
                'status' => $location->status,
                '_lft' => $location->_lft,
                '_rgt' => $location->_rgt,
                'parent_id' => $location->parent_id,
                'create_user' => $location->create_user,
                'update_user' => $location->update_user,
                'deleted_at' => $location->deleted_at,
                'origin_id' => $location->origin_id,
                'lang' => $location->lang,
                'created_at' => $location->created_at,
                'updated_at' => $location->updated_at,
                'banner_image_id' => $location->banner_image_id,
                'trip_ideas' => $location->trip_ideas,
                'image' => $image,
            ];
        }

        return response()->json([
            'location' => $locationData,
            'message' => 'Success',
            'status' => 1,
            'success' => true

        ]);
    }
    public function tours()
    {
        $tours = Tour::get();
        $tourIds = $tours->pluck('id');
        $TourTranslations = TourTranslation::whereIn('origin_id', $tourIds)->where('locale', 'en')->get();

        $tourData = [];

        foreach ($tours as $tour) {
            $image = MediaFile::find($tour->banner_image_id);
            if ($image) {
                $image = "uploads/" . $image->file_path;
            }
            $location = Location::find($tour->location_id);
            $location_name = $location->name;

            $translation = TourTranslation::where('origin_id', $tour->id)->first();

            $location_transalte = LocationTranslation::where('origin_id', $location->id)->where('locale', 'en')->first();


            $tours2[] = [
                'id' => $tour['id'],
                'name' => $tour['title'],
                'name_en' => $translation->title??"",
                'image' => $image,
                'location' => $location_name,
                'location_en' => $location_transalte->name,

            ];
        }
        $categories = TourCategory::with('tour')->get();

        $categoryArray = [];

        foreach ($categories as $category) {
            $image = MediaFile::find($category->banner_image_id);
            if ($image) {
                $image = "uploads/" . $image->file_path;
            } else {
                $image = null;
            }
            $translation_category = TourCategoryTranslation::where('origin_id', $category->id)->where('locale', 'en')->first();
            $categoryData = [
                'id' => $category->id,
                'name' => $category->name,
                'name_en' => $translation_category->name,
                'content' => $category->content,
                'content_en' => $translation_category->content,
                'video' => $category->video,
                'image' => $image,
                'tours' => [],
            ];

            foreach ($category->tour as $tour) {
                $image = MediaFile::find($tour->banner_image_id);
                if ($image) {
                    $image = "uploads/" . $image->file_path;
                }
                $location = Location::find($tour->location_id);
                $location_name = $location->name;

                $translation = TourTranslation::where('origin_id', $tour->id)->first();

                $location_transalte = LocationTranslation::where('origin_id', $location->id)->where('locale', 'en')->first();



                $location = Location::find($tour->location_id);
                $location_name = $location->name;
                $tourData = [
                    'id' => $tour['id'],
                    'name' => $tour['title'],
                    'name_en' => $translation->title??"",
                    'image' => $image,
                    'location' => $location_name,
                    'location_en' => $location_transalte->name,
                    'location_id' => $tour['location_id'],

                ];

                $categoryData['tours'][] = $tourData;
            }

            $categoryArray[] = $categoryData;
        }


        return response()->json([
            'tour' => $tours2,
            'catgeories' => $categoryArray,
            'message' => 'Success',
            'status' => 1,
            'success' => true

        ]);
    }
    public function get_tours($location_id, $category_id)
    {
        $tours = Tour::where('location_id', $location_id)->where('category_id', $category_id)->get();

        $tourIds = $tours->pluck('id');

        $tourData = [];

        foreach ($tours as $tour) {
            $translation = TourTranslation::where('origin_id', $tour->id)->where('locale', 'en')->first();
            $image = MediaFile::find($tour->banner_image_id);
            if ($image) {
                $image = "uploads/" . $image->file_path;
            }
            $tourData[] = [
                'id' => $tour['id'],
                'name' => $tour['title'],
                'name_en' => $translation->title,
                'image' => $image,
                'review_score' => $tour['review_score'],
            ];
        }

        return response()->json([
            'tour' => $tourData,
            'message' => 'Success',
            'status' => 1,
            'success' => true

        ]);
    }
    public function get_images($id)
    {
        $tour = Tour::where('id', $id)->select('banner_image_id')->first();
        $image = MediaFile::where('id', $tour->banner_image_id)->select('file_path')->first();
        return response()->json([
            'image' => "uploads/" . $image->file_path,
            'message' => 'Success',
            'status' => 1,
            'success' => true

        ]);
    }
    public function add_to_wishlist(Request $request)
    {
        $row = new UserWishList();
        $row->user_id = $request->input('user_id');
        $row->object_id = $request->input('trip_id');
        $row->object_model = "tour";
        $row->save();
        $wishlist = Tour::where('id', $row->object_id)->first();
        $image = MediaFile::find($wishlist->banner_image_id);
        if ($image) {
            $image = "uploads/" . $image->file_path;
        }
        $tourData[] = [
            'id' => $wishlist['id'],
            'name' => $wishlist['title'],
            'image' => $image,
        ];
        return response()->json([
            'tour' => $tourData,
            'message' => 'Success',
            'status' => 1,
            'success' => true

        ]);
    }
    public function remove_from_wishlist(Request $request)
    {

        $row = UserWishList::where('user_id', $request->user_id)->where('object_id', $request->trip_id)->first();
        $row->delete();
        $wishlist = UserWishList::where('user_id', $request->user_id)->get();
        $count = $wishlist->count();
        if ($count > 0) {
            $tourData = [];
            foreach ($wishlist as $wishlist) {
                $tour = Tour::where('id', $wishlist->object_id)->first();
                $image = MediaFile::find($tour->banner_image_id);
                if ($image) {
                    $image = "uploads/" . $image->file_path;
                }
                $tourData[] = [
                    'id' => $tour['id'],
                    'name' => $tour['title'],
                    'image' => $image,
                ];
            }

            return response()->json([
                'tour' => $tourData,
                'message' => 'Success',
                'status' => 1,
                'success' => true

            ]);
        } else {
            return response()->json([
                'message' => 'No Wishlist for this user',
                'status' => 0,
                'success' => false

            ]);
        }
        return response()->json([
            'wishlist' => $row,
            'message' => 'Success',
            'status' => 1,
            'success' => true

        ]);
    }
    public function getFavorite($id)
    {
        $wishlist = UserWishList::where('user_id', $id)->get();
        $count = $wishlist->count();
        if ($count > 0) {
            $tourData = [];
            foreach ($wishlist as $wishlist) {
                $tour = Tour::where('id', $wishlist->object_id)->first();
                $tour_en = TourTranslation::where('origin_id', $wishlist->object_id)->where('locale', 'en')->first();
                $image = MediaFile::find($tour->banner_image_id);
                if ($image) {
                    $image = "uploads/" . $image->file_path;
                }
                $tourData[] = [
                    'id' => $tour['id'],
                    'name' => $tour['title'],
                    'name_en' => $tour_en->title,
                    'image' => $image,
                ];
            }
            $tourData = collect($tourData);
            $tourIds = $tourData->pluck('id');

            return response()->json([
                'tour' => $tourData,
                'message' => 'Success',
                'status' => 1,
                'success' => true

            ]);
        } else {
            return response()->json([
                'message' => 'No Wishlist for this user',
                'status' => 0,
                'success' => true

            ]);
        }


        if ($wishlist) {
        }
    }
    public function get_coupons($id, $booking_id, $service_id)
    {
        $booking = Booking::find($booking_id);
        $service = CouponServices::where('object_id', $service_id)->get();
        $serviceIds = $service->pluck('service_id')->toArray();
        $couponsById = Coupon::where(function ($query) use ($id) {
            $query->whereNull('only_for_user')
                ->orWhereJsonContains('only_for_user', $id);
        })->where('status', 'publish')
            ->where('end_date', '>=', Carbon::now()->format('Y-m-d H:i:s'))
            ->get();
        $couponsByService = Coupon::where(function ($query) use ($serviceIds) {
            $query->whereNull('services')
                ->orWhere(function ($subquery) use ($serviceIds) {
                    foreach ($serviceIds as $serviceId) {
                        $subquery->orWhere('services', 'LIKE', '%"' . $serviceId . '"%');
                    }
                });
        })->where('status', 'publish')->where('end_date', '>=', Carbon::now()->format('Y-m-d H:i:s'))->get();
        $coupons = collect($couponsById)->intersect($couponsByService)->values();
        $coupons = $coupons->toArray();
        $couponCodes = collect($couponsById)->intersect($couponsByService)->pluck('code')->toArray();
        $couponsExceedingLimit = [];
        $couponUsage = [];

        foreach ($couponCodes as $couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            $usageCount = CouponBookings::where('coupon_code', $couponCode)
                ->where('create_user', $id)
                ->whereNotIn('booking_status', ['draft', 'unpaid', 'cancelled'])
                ->count();
            $couponUsage[$couponCode] = $usageCount;
            if ($usageCount > $coupon->quantity_limit && $usageCount > $coupon->limit_for_user && $coupon->min_total <= $booking->total && $coupon->max_total >= $booking->total) {
                $couponsExceedingLimit[] = $couponCode;
            }
        }
        $couponData = [];

        foreach ($couponUsage as $couponCode => $usageCount) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon) {
                $image = MediaFile::find($coupon->image_id);
                $coupon->image = "uploads/" . $image->file_path;
                $couponData[] = [
                    'id'            => $coupon->id,
                    'name'          => $coupon->name,
                    'code'          => $coupon->code,
                    'amount'        => $coupon->amount,
                    'image'         => $coupon->image,
                    'discount_type' => $coupon->discount_type,
                    'end_date'      => $coupon->end_date,
                    'min_total'     => $coupon->min_total,
                    'max_total'     => $coupon->max_total,
                    'status'        => $coupon->status,
                    'created_at'    => $coupon->created_at,
                    'updated_at'    => $coupon->updated_at,
                    'quantity_limit' => $coupon->quantity_limit,
                    'limit_per_user' => $coupon->limit_per_user,
                    'deleted_at'    => $coupon->deleted_at,
                    'update_user'   => $coupon->update_user,
                    'create_user'   => $coupon->create_user,
                    'services'      => $coupon->services,
                    'only_for_user' => $coupon->only_for_user
                ];
            }
        }

        if ($couponData) {
            return response()->json([
                'coupons' => $couponData,
                'message' => 'Success',
                'status' => 1,
                'success' => true

            ]);
        } else {
            return response()->json([
                'coupons' => [],
                'message' => 'No Coupons',
                'status' => 0,
                'success' => false

            ]);
        }
    }
    // public function tour_detail(Request $request, $id)
    // {
    //     $tour = Tour::find($id);
    //     $tour_en = TourTranslation::where('origin_id',$id)->where('locale','en')->first();
    //     if ($tour) {
    //         $galleryImageIds = explode(',', $tour->gallery);

    //         $galleryPaths = MediaFile::whereIn('id', $galleryImageIds)
    //             ->pluck('file_path')
    //             ->map(function ($filePath) {
    //                 return 'uploads/' . $filePath;
    //             })
    //             ->toArray();
    //     }
    //     $bookings = Bookning_dates::where('tour_id', $tour->id)->where('category_id', $tour->category_id)->where('active', 1)->get();
    //     $time_slots = CategoryTimeSlot::where('category_id', $tour->category_id)->where('tour_id', $tour->id)->get();
    //     $reservedDays = [];
    //     foreach ($bookings as $booking) {
    //         $day = $booking['day'];
    //         $reserved = true;

    //         foreach ($time_slots as $time_slot) {
    //             if ($booking['start_from'] >= $time_slot['start_at'] && $booking['end_at'] <= $time_slot['end_at']) {
    //                 $reserved = true;
    //                 break;
    //             }
    //         }

    //         if ($reserved) {
    //             $reservedDays[] = $day;
    //         }
    //     }
    //     $reservedDays = array_unique($reservedDays);
    //     $reservedDays = (object) $reservedDays;
    //     $menuIds = json_decode($tour->menu_id);
    //     if($menuIds){
    //       $menus = MenuTour::whereIn('id', $menuIds)->get();
    //       foreach ($menus as $menu) {
    //           $menuItems = Terms::where('menu_id', $menu->id)->get();
    //           $menu_extra = MenuExtras::where('menu_id', $menu->id)->get();
    //           $menu->terms = $menuItems;
    //           $menu->extra_terms = $menu_extra;
    //         }
    //     }else{
    //         $menus= [];
    //     }
    //     if ($tour->menu_id === "null") {
    //         $have_menu = "0";

    //     } else {
    //         $have_menu = "1";

    //     }
    //     $meta = TourMeta::where('tour_id', $id)->get();
    //     return response()->json([
    //         'tourDetails' => [
    //             'tour' => $tour,
    //             'string' => "Create Order",
    //             'translation' => $tour_en,
    //             'gallery' => $galleryPaths,
    //             'extras' => $meta,
    //             'menuDetails' => $menus,
    //             'UnAvailableDates' => $reservedDays,
    //             'have_menu' => $have_menu,

    //         ],
    //     ]);
    // }
    public function tour_detail(Request $request, $id)
    {
        $tour = Tour::find($id);
        $tour_en = TourTranslation::where('origin_id', $id)->where('locale', 'en')->first();
        $tour->title_en = $tour_en->title;
        $tour->content_en = $tour_en->content;
        $tour->short_desc_en = $tour_en->short_desc;
        $tour->faqs_en = $tour_en->faqs;
        $tour->address_en = $tour_en->address;
        $tour->include_en = $tour_en->include;
        if ($tour) {
            $galleryImageIds = explode(',', $tour->gallery);
            $galleryPaths = MediaFile::whereIn('id', $galleryImageIds)
                ->pluck('file_path')
                ->map(function ($filePath) {
                    return 'uploads/' . $filePath;
                })->toArray();
        }
        $bookings = Bookning_dates::where('tour_id', $tour->id)
            ->where('category_id', $tour->category_id)
            ->where('active', 1)
            ->get();
        $reservedDays = [];
        $bookings = json_decode($bookings, true);
        $dayCounts = [];

        foreach ($bookings as $booking) {
            $day = $booking['day'];
            if (isset($dayCounts[$day])) {
                $dayCounts[$day]++;
            } else {
                $dayCounts[$day] = 1;
            }
        }
        $result = [];
        foreach ($dayCounts as $day => $count) {
            $result[] = "Day: $day, Count: $count";
        }
        $time_slots = CategoryTimeSlot::where('category_id', $tour->category_id)
            ->where('tour_id', $tour->id)
            ->count();
        foreach ($dayCounts as $day => $count) {
            $result[] = "Day: $day, Count: $count";
            if ($count == $time_slots) {
                $reservedDays[] = $day;
            }
        }

        $menuIds = json_decode($tour->menu_id);
        if ($menuIds) {
            $menus = MenuTour::whereIn('id', $menuIds)->get();
            foreach ($menus as $menu) {
                $MenuTranslations = MenusTranslation::where('origin_id', $menu->id)->where('locale', 'en')->first();
                $menu->name_en = $MenuTranslations->name ?? '';
                $menu->description_en = $MenuTranslations->description ?? '';
                $menuItems = Terms::where('menu_id', $menu->id)->get();
                $menu_extra = MenuExtras::where('menu_id', $menu->id)->get();
                $menu->terms = $menuItems;
                $menu->extra_terms = $menu_extra;
            }
        } else {
            $menus = [];
        }
        if ($tour->menu_id === "null") {
            $have_menu = "0";
        } else {
            $have_menu = "1";
        }
        $meta = TourMeta::where('tour_id', $id)->get();
        return response()->json([
            'tourDetails' => [
                'tour' => $tour,
                'string' => "Create Order",
                'gallery' => $galleryPaths,
                'extras' => $meta,
                'menuDetails' => $menus,
                'UnAvailableDates' => $reservedDays,
                'have_menu' => $have_menu,
                'success' => true


            ],
        ]);
    }
    public function  category_time(Request $request)
    {
        $categories = TourCategory::select('id', 'start_from', 'end_at')->get();
        return response()->json([
            'categories' => $categories,
            'success' => true

        ]);
    }
    public function tour_time(Request $request, $id)
    {
        $tour = Tour::where('category_id', $id)->select('id', 'title', 'banner_image_id', 'address')->get();

        foreach ($tour as $tour) {
            $image = MediaFile::find($tour->banner_image_id);
            if ($image) {
                $image = "uploads/" . $image->file_path;
            }
            $TourTranslations = TourTranslation::where('origin_id', $tour->id)->where('locale', 'en')->first();


            $tourData[] = [
                'id' => $tour['id'],
                'name' => $tour['title'],
                'name_en' => $TourTranslations->title,
                'image' => $image,
                'address' => $tour['address'],

                'address_en' => $TourTranslations->address,
            ];
        }
        return response()->json([
            'tours' => $tourData,
            'success' => true

        ]);
    }


    // public function time_slot(Request $request, $day, $tour_id)
    // {
    //     $booking_dates = Bookning_dates::where('day', $day)->where('tour_id', $tour_id)->where('active', 1)->get();
    //     $time_slots = CategoryTimeSlot::where('tour_id', $tour_id)->get();
    //     $reservedTimeSlots = [];
    //     foreach ($booking_dates as $booking_date) 
    //     {
    //         $reservedTimeSlots[] = [
    //             'start_at' => $booking_date->start_from,
    //             'end_at' => $booking_date->end_at
    //         ];
    //     }
    //     $availableTimeSlots = [];
    //     foreach ($time_slots as $time_slot) {
    //         $isReserved = false;
    //         foreach ($reservedTimeSlots as $reservedTimeSlot) {
    //             if ($reservedTimeSlot['start_at'] == $time_slot->start_at && $reservedTimeSlot['end_at'] == $time_slot->end_at) {
    //                 $isReserved = true;
    //                 break;
    //             }
    //         }
    //         if ($isReserved==false) {
    //             $availableTimeSlots[] = $time_slot;
    //         }
    //     }

    //     return response()->json([
    //         'time_slot' => $availableTimeSlots,
    //         'success' => true

    //     ]);
    // }

    public function time_slot(Request $request, $day, $tour_id)
    {
        $booking_dates = Bookning_dates::where('day', $day)
            ->where('tour_id', $tour_id)
            ->where('active', 1)
            ->get();
            

        $time_slots = CategoryTimeSlot::where('tour_id', $tour_id)->get();
        $reservedTimeSlots = [];
        foreach ($booking_dates as $booking_date) {
            $reservedTimeSlots[] = [
                'start_at' => $booking_date->start_from,
                'end_at' => $booking_date->end_at,
            ];
        }
        $availableTimeSlots = [];
        foreach ($time_slots as $time_slot) {
            $isReserved = false;
            foreach ($reservedTimeSlots as $reservedTimeSlot) {
                if ($reservedTimeSlot['start_at'] == $time_slot->start_at && $reservedTimeSlot['end_at'] == $time_slot->end_at) {
                    $isReserved = true;
                    break;
                }
            }
            if (!$isReserved) {
                $availableTimeSlots[] = [
                    'id' => $time_slot->id,
                    'tour_id' => $time_slot->tour_id,
                    'category_id' => $time_slot->category_id,
                    'start_at' => substr($time_slot->start_at, 0, 5),
                    'end_at' => substr($time_slot->end_at, 0, 5)
                ];
            }
        }
        return response()->json([
            'time_slot' => $availableTimeSlots,
            'success' => true
        ]);
    }


    public function all_menus(Request $request, $id)
    {
        $tour = Tour::find($id);
        $menuIds = json_decode($tour->menu_id, true);

        if ($menuIds == null) {
            return response()->json([
                'all_menus' => [],
                'extra_terms' => [],
                'success' => false
            ]);
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
                    $menu->name_en = $menu_en->name ?? '';
                    $menu->description_en = $menu_en->description ?? '';

                    $menu->terms = Terms::where('menu_id', $menu->id)->get();
                    foreach ($menu->terms as $term) {
                        $term_en = TermsTranslation::where('origin_id', $term->id)->where('locale', 'en')->first();
                        $term->name_en = $term_en->name ?? '';
                        $term->description_en = $term_en->description ?? '';
                        $mediaFile = MediaFile::find($term->image_id);
                        if ($mediaFile) {
                            $term->image_path = "uploads/" . $mediaFile->file_path;
                        }
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
            return response()->json([
                'all_menus' => $menusWithChildren,
                'extra_terms' => $extra_terms_last,
                'success' => true
            ]);
        }
    }
    public function new_booking(Request $request)
    {

        try {
            DB::beginTransaction();
            $user = User::find($request->userId);
            $category = Tour::find($request->tourId);
            $booking = new Booking();
            $booking->code = md5(uniqid() . rand(0, 99999));
            $booking->customer_id = $request->userId;
            $booking->vendor_id = $category->author_id;
            $booking->object_id = $request->tourId;
            $booking->category_id = $category->category_id;
            $booking->object_model = "tour";
            $booking->start_date = $request->selectedDate;
            $booking->end_date =  $request->selectedDate;
            $booking->total = $request->totalPrice;
            $booking->total_guests = $request->countPerson;
            $booking->currency = "EGP";
            $booking->status = "pending";
            $booking->email = $user->email;
            $booking->first_name = $user->first_name;
            $booking->last_name = $user->last_name;
            $booking->phone = $user->phone;
            $booking->address = $user->address;
            $booking->address2 = $user->address2;
            $booking->city = $user->city;
            $booking->state = $user->state;
            $booking->zip_code = $user->zip_code;
            $booking->country = $user->country;
            $booking->customer_notes = $request->customer_notes;
            $booking->create_user = $request->userId;
            $booking->total_before_fees = $request->totalPrice;
            $booking->paid = 0.00;
            $booking->total_before_discount = $request->totalPrice;
            $booking->save();
            $booking_date = new Bookning_dates();
            $booking_date->tour_id = $request->tourId;
            $booking_date->category_id = $category->category_id;
            $booking_date->booking_id = $booking->id;
            $booking_date->start_from = $request->start_at;
            $booking_date->end_at = $request->end_at;
            $booking_date->create_user = 1;
            $booking_date->active = 0;
            $booking_date->day = $request->selectedDate;
            $booking_date->save();
            DB::table('bravo_booking_meta')->insert([
                'name'       => "person_types",
                'val'        => json_encode($request->personType),
                'booking_id' => $booking->id
            ]);
            DB::table('bravo_booking_meta')->insert([
                'name'       => "extra_price",
                'val'        => json_encode($request->extra),
                'booking_id' => $booking->id
            ]);
            DB::table('bravo_booking_meta')->insert([
                'name'       => "base_price",
                'val'        => $booking->total / $booking->total_guests,
                'booking_id' => $booking->id
            ]);
            DB::table('bravo_booking_meta')->insert([
                'name'       => "menus",
                'val'        => json_encode($request->menus_specialize),
                'booking_id' => $booking->id
            ]);
            DB::table('bravo_booking_meta')->insert([
                'name'       => "extra_menus",
                'val'        => json_encode($request->extra_menus),
                'booking_id' => $booking->id
            ]);
            $bookingMeta = DB::table('bravo_booking_meta')->where('booking_id', $booking->id)->where('name', 'extra_price')->first();
            if ($bookingMeta->val != null) {
                $extra = json_decode($bookingMeta->val);
                $time = json_decode($extra);
                foreach ($time as $extra_time) {
                    if ($extra_time->type == "per_hour") {
                        $last_booking_dates = Bookning_dates::where('booking_id', $booking->id)->first();
                        $booking_date = new Bookning_dates();
                        $booking_date->create_user = 1;
                        $booking_date->tour_id = $request->tourId;
                        $booking_date->category_id = $category->category_id;
                        $booking_date->booking_id = $booking->id;
                        $booking_date->start_from = $request->end_at;
                        $booking_date->end_at = Carbon::parse($booking_date->start_date)->addHours(2);
                        $booking_date->active = 0;
                        $booking_date->day = $request->selectedDate;
                        $booking_date->save();
                    }
                }
            }
            $deposit = Settings::where('name', 'tour_deposit_enable')->first();
            if ($deposit->val == 1) {
                $type = Settings::where('name', 'tour_deposit_type')->first();
                $amount = Settings::where('name', 'tour_deposit_amount')->first();
                $type = $type->val;
                $amount = $amount->val;
            }
            if ($category->enable_service_fee == 1) {

                $service_fee = $category->service_fee;
            } else {
                $service_fee = [];
            }
            $myfatoora_token = Settings::where('name', 'myfatoora_token')->first();
            $myfatoora_enabled = Settings::where('name', 'myfatoora_live')->first();
            DB::commit();
            return response()->json([
                "message" => true,
                "booking_id" => $booking->id,
                "enable_cash" => 1,
                'success' => true,
                'service_fee' => $service_fee,
                'deposit' => [
                    'enabled' => $deposit->val,
                    'type' => $type ?? null,
                    'amount' => $amount ?? null,
                    'enable_cash' => 0,
                    'enable_myfatoora' => 1,
                    'myfatoora_live' => 0,
                    'my_fatoora_token' => "rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL",
                    'service_fee'  => $service_fee,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                "message" => false,
                "error_message" => $e->getMessage(),
                'success' => false
            ]);
        }
    }
    public function update_booking(Request $request)
    {

        DB::beginTransaction();
        try {
            $booking = Booking::where('id', $request->bookingId)->first();
            $coupon = Coupon::where('id', $request->couponId)->first();
            $amount = 0.00;

            if ($request->couponId != null) {
                if ($coupon->discount_type == "fixed") {
                    $amount = $coupon->amount;
                } else {
                    $booking_total = $booking->total;
                    $amount = $booking_total / $coupon->amount;
                }
                $booking_coupon = new CouponBookings();
                $booking_coupon->booking_id = $booking->id;
                $booking_coupon->booking_status = "processing";
                $booking_coupon->object_id = $booking->object_id;
                $booking_coupon->object_model = $booking->object_model;
                $booking_coupon->coupon_code = $coupon->code;
                $booking_coupon->coupon_amount = $amount;
                $booking_coupon->coupon_data = $coupon;
                $booking_coupon->create_user = $booking->customer_id;
                $booking_coupon->save();
            }
            $deposit = $request->input('deposit', null);
            if ($deposit && $deposit['enabled'] == 1) {
                $total_paid = $deposit['depositPrice'];
                $booking_deposit = $deposit['depositPrice'];
                $booking_deposit_type = $deposit['type'];
            } else {
                $booking_deposit = null;
                $booking_deposit_type = null;
                $total_paid = $deposit['totalAfterCouponAndFee'];
            }
            $booking->status = "processing";
            $booking->coupon_amount = $amount;
            $booking->total = $deposit['totalAfterCouponAndFee'];
            $booking->is_paid = 1;
            $booking->paid = $total_paid;
            $booking->pay_now = $total_paid;

            $booking->gateway = "myfatoora";
            $booking->deposit = $booking_deposit;
            $booking->deposit_type = $booking_deposit_type;
            $booking->payment_id = $request->paymentId;
            $booking->total_before_fees = $deposit['totalAfterOnlyCoupon'];

            $booking->save();

            $booking_date = Bookning_dates::where('booking_id', $request->bookingId)->get();
            foreach ($booking_date as $date) {
                $date->active = 1;
                $date->save();
            }

            $invoice = route('user.booking.invoice', $booking->code);
            $user = User::find($booking->customer_id);
            $token = $user->token;

            $content_en = " Booked Successfully. Please confirm your reservation";

            $content = "لقد تم ارسال الحجز الخاص بك بنجاح برجاء تأكيد الحجز" . '+' . $content_en;

            $data = [
                $booking->id,
            ];
            $send = $this->notifybyfirebase($content, $content_en, $token, $data);
            $firebase_notification = [
                'booking_id' => $booking->id,
                'content' => $content,
                'content_en' => $content_en,
                'title' => 'Imkan',
                'user_id' => $user->id,
            ];
            DB::table('firebase_notifications')->insert($firebase_notification);
            DB::commit();
            return response()->json([
                "message" => "success",
                "invoice" => $invoice,
                'success' => true
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => "error",
                "error" => $e->getMessage(),
                'success' => false

            ]);
        }
    }
    public function category_by_time(Request $request, $time, $id)
    {
        $categories = TourCategory::where('start_from', '<=', $time)
            ->where('end_at', '>=', $time)
            ->get();
        $categoryIds = $categories->pluck('id');
        $tours = Tour::whereIn('category_id', $categoryIds)->where('location_id', $id)->get();
        $tourData = [];
        foreach ($tours as $tour) {
            $TourTranslations = TourTranslation::where('origin_id', $tour->id)->where('locale', 'en')->first();
            $image = MediaFile::find($tour->banner_image_id);
            if ($image) {
                $image = "uploads/" . $image->file_path;
            }
            $tourData[] = [
                'id' => $tour['id'],
                'name' => $tour['title'],
                'name_en' => $TourTranslations->title,
                'image' => $image,
                'review_score' => $tour['review_score'],
            ];
        }
        $tourData = collect($tourData);
        $tourIds = $tourData->pluck('id');
        return response()->json([
            'tour' => $tourData,
            'message' => 'Success',
            'status' => 1,
            'success' => true

        ]);
    }
    public function get_all_reviews(Request $request, $id)
    {
        $reviews1 = Review::where('object_id', $id)->where('rate_number', 1)->where("status", "approved")->with('services')->get();
        $reviews2 = Review::where('object_id', $id)->where('rate_number', 2)->where("status", "approved")->with('services')->get();
        $reviews3 = Review::where('object_id', $id)->where('rate_number', 3)->where("status", "approved")->with('services')->get();
        $reviews4 = Review::where('object_id', $id)->where('rate_number', 4)->where("status", "approved")->with('services')->get();
        $reviews5 = Review::where('object_id', $id)->where('rate_number', 5)->where("status", "approved")->with('services')->get();
        return response()->json([
            "message" => "Success",
            "1" => $reviews1,
            "2" => $reviews2,
            "3" => $reviews3,
            "4" => $reviews4,
            "5" => $reviews5,
            'success' => true

        ]);
    }
    public function all_booking(Request $request, $id)
    {
        $bookings = DB::table('bravo_bookings')
            ->join('bravo_tours', 'bravo_bookings.object_id', '=', 'bravo_tours.id')
            ->join('media_files', 'bravo_tours.banner_image_id', '=', 'media_files.id')
            ->join('booking_dates', 'bravo_bookings.id', '=', 'booking_dates.booking_id')
            ->join('bravo_locations', 'bravo_tours.location_id', '=', 'bravo_locations.id')
            ->join('bravo_location_translations', function ($join) {
                $join->on('bravo_locations.id', '=', 'bravo_location_translations.origin_id')
                    ->where('bravo_location_translations.locale', 'en');
            })
            ->join('bravo_tour_translations', function ($join) {
                $join->on('bravo_tours.id', '=', 'bravo_tour_translations.origin_id')
                    ->where('bravo_location_translations.locale', 'en');
            })
            ->where('bravo_bookings.customer_id', $id)
            ->select(
                'bravo_bookings.id',
                'bravo_tours.title as tour_name',
                'bravo_tours.review_score as review_score',
                'bravo_tour_translations.title as tour_name_en',
                'bravo_tours.id as tour_id',
                'bravo_bookings.start_date',
                'bravo_bookings.total_guests',
                'bravo_bookings.total',
                'bravo_locations.name as location_name',
                'bravo_location_translations.name as location_name_en',
                DB::raw("CONCAT('uploads/', media_files.file_path) as tour_image_path"),
                DB::raw('(SELECT start_from FROM booking_dates WHERE booking_id = bravo_bookings.id ORDER BY id LIMIT 1) as start_from'),
                DB::raw('(SELECT end_at FROM booking_dates WHERE booking_id = bravo_bookings.id ORDER BY id DESC LIMIT 1) as end_at'),
                'bravo_bookings.status as status'
            )
            ->distinct()
            ->get();



        $booking = Booking::where('customer_id', $id)->select('id', 'total', 'total_guests', 'status', 'object_id', 'start_date')->get();

        $UserBooking = [];

        foreach ($booking as $booking) {
            $tour = Tour::where('id', $booking->object_id)->first();
            $tour_en = TourTranslation::where('origin_id', $tour->id)->where('locale', 'en')->first();
            $location = Location::where('id', $tour->location_id)->first();
            $location_en = LocationTranslation::where('origin_id', $tour->location_id)->where('locale', 'en')->first();
            $booking_dates = Bookning_dates::where('booking_id', $booking->id)->get();


            if ($booking_dates->count() > 1) {
                $start_from = $booking_dates[0]['start_from'];
                $end_at = $booking_dates[1]['end_at'];
            } else {
                $start_from = $booking_dates[0]['start_from'];
                $end_at = $booking_dates[0]['end_at'];
            }

            $image = MediaFile::find($tour->banner_image_id);
            $booking->tour_name = $tour->title;
            $booking->tour_name_en = $tour_en->title;
            $booking->tour_id = $tour->id;
            $booking->review_score = $tour->review_score;
            $booking->start_date = $booking->start_date;
            $booking->total_guests = $booking->total_guests;
            $booking->total = $booking->total;
            $booking->location_name = $location->name;
            $booking->location_name_en = $location_en->name;
            $booking->start_from = $start_from;
            $booking->end_at = $end_at;
            $booking->status = $booking->status;
            $booking->tour_image_path = "uploads/" . $image->file_path;
            $UserBooking[] = $booking;
        }
        $services = Settings::where('id', 101)->first();
        $values = $services->val;
        return response()->json([
            "booking" => $UserBooking,
            "service_review" => json_decode($values),
            'success' => true
        ]);
    }
    public function add_review(Request $request)
    {

        DB::beginTransaction();
        try {
            $review = new Review();
            $review->object_id = $request->tourId;
            $review->object_model = "tour";
            $review->title = $request->title;
            $review->content = $request->content;
            $review->status = "approved";
            $review->publish_date = Carbon::now();
            $review->author_id = $request->userId;
            $review->save();
            $services = json_decode($request->services);
            foreach ($services as $service) {
                $review_meta = new ReviewMeta();
                $review_meta->review_id = $review->id;
                $review_meta->object_id = $review->object_id;
                $review_meta->object_model = "tour";
                $review_meta->name = $service->title;
                $review_meta->val = $service->rate;
                $review_meta->create_user = 1;
                $review_meta->save();
            }
            $count = ReviewMeta::where('review_id', $review->id)->count();
            $rate = ReviewMeta::where('review_id', $review->id)->sum('val');
            $review->rate_number = round($rate / $count);
            $review->save();


            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = $image->store('/reviews');
                $imagePath =  "uploads/" . $imagePath;
            } else {
                $image = '';
                $imagePath = '';
            }

            $review_meta = new ReviewMeta();
            $review_meta->review_id = $review->id;
            $review_meta->object_id = $review->object_id;
            $review_meta->object_model = "tour";
            $review_meta->name = "upload_picture";
            $review_meta->val = $imagePath;
            $review_meta->create_user = 1;
            $review_meta->save();
            $module = Tour::find($review->object_id);
            $module = Tour::find($review->object_id);
            $rateData = Review::selectRaw("AVG(rate_number) as rate_total")->where('object_id', $review->object_id)->where('object_model', $review->object_model)->where("status", "approved")->first();
            $rate_number = number_format($rateData->rate_total ?? 0, 1);
            $module->review_score = $rate_number;
            $module->save();
            DB::commit();
            return response()->json([
                "message" => "Review added successfully",
                "review_id" => $review->id,
                'success' => true
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                "message" => "error",
                "error_message" => $e->getMessage(),
                'success' => false

            ]);
        }
    }
    public function search(Request $request, $seach_query, $category_id, $lang)
    {
        if ($lang === "en") {
            $tours = TourTranslation::where('title', 'like', '%' . $seach_query . '%')->where('locale', 'en')->get();
            if ($tours->count() > 0) {
                $tourData = [];
                foreach ($tours as $tour) {
                    $image_id = Tour::where('id', $tour->origin_id)->where('category_id', $category_id)->first();
                    if ($image_id != null) {
                        $image = MediaFile::where('id', $image_id->banner_image_id)->first();
                        if ($image) {
                            $image = "uploads/" . $image->file_path;
                        } else {
                            $image = "";
                        }
                    } else {
                        $image = "";
                    }

                    $tours2[] = [
                        'id' => $tour['origin_id'],
                        'name' => $tour['title'],
                        'image' => $image,
                    ];
                }
                return response()->json([
                    "message" => "Success",
                    "tours" => $tours2,
                    'success' => true

                ]);
            } else {
                return response()->json([
                    "message" => "No Tours Found",
                    'success' => false

                ]);
            }
        } else {
            $tours = Tour::where('title', 'like', '%' . $seach_query . '%')->where('category_id', $category_id)->get();
            if ($tours->count() > 0) {
                $tourData = [];
                foreach ($tours as $tour) {
                    $image = MediaFile::find($tour->banner_image_id);
                    if ($image) {
                        $image = "uploads/" . $image->file_path;
                    }
                    $tours2[] = [
                        'id' => $tour['id'],
                        'name' => $tour['title'],
                        'image' => $image,
                    ];
                }
                return response()->json([
                    "message" => "Success",
                    "tours" => $tours2,
                    'success' => true

                ]);
            } else {
                return response()->json([
                    "message" => "No Tours Found",
                    'success' => false

                ]);
            }
        }
    }
    public function cancel_booking(Request $request)
    {
        $booking = Booking::find($request->bookingId);
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
    public function get_deposit(Request $request, $id)
    {

        $category = Tour::find($id);
        if ($category->enable_service_fee == 1) {
            $service_fee = $category->service_fee;
        } else {
            $service_fee = [];
        }
        $myfatoora_token = Settings::where('name', 'myfatoora_token')->first();
        $myfatoora_enabled = Settings::where('name', 'myfatoora_live')->first();

        $deposit = Settings::where('name', 'tour_deposit_enable')->first();
        if ($deposit->val == 1) {
            $type = Settings::where('name', 'tour_deposit_type')->first();
            $amount = Settings::where('name', 'tour_deposit_amount')->first();
            $type = $type->val;
            $amount = $amount->val;
        }

        return response()->json([
            'success' => true,
            'deposit' => [
                'enabled' => $deposit->val,
                'type' => $type ?? null,
                'amount' => $amount ?? null,
                'enable_cash' => 1,
                'enable_myfatoora' => 0,
                'myfatoora_live' => 0,
                'my_fatoora_token' => "rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL",

                'service_fee'  => $service_fee,

            ],
        ]);
    }
    public function delete_account(Request $request)
    {
        $user = User::find($request->input("userId"));
        if (!$user) {
            return response()->json(["success" => false, 'status' => 500, "message" => "Failed To Delete Account"]);
        }
        $user->email = $user->email . "deleted_account" . Str::random('20');
        $user->phone = $user->phone . "deleted_account" . Str::random('20');
        $user->name = $user->name . "deleted_account";
        $user->save();
        return response()->json(["success" => true, 'status' => 200, "message" => "Account Deleted Successfully"]);
    }
    public function check_activation(Request $request, $id)
    {
        $date = Bookning_dates::where('booking_id', $id)->get();
        


        $data = json_decode($date, true);
        $ids = array_column($data, 'id');
        $day = $date[0]['day'];
        $object_id = Booking::find($id);
        $booking_active = Bookning_dates::where('day', $day)->where('tour_id', $object_id->object_id)->where('active', 1)->get();
        foreach ($booking_active as $booking_active) {
            if ($booking_active->start_from == $date[0]['start_from']) {
                return response()->json([
                    "success" => false,
                    "mesaage" => "Your Booking is Not Available Try To Create New Booking",
                ]);
            } else {
                $secondDate = $date->first(function ($item) use ($date) {
                    return $item !== $date[0];
                });
                if ($secondDate && $booking_active->start_from == $secondDate['start_from']) {
                    return response()->json([
                        "success" => false,
                        "message" => "Your Booking is Not Available. Try to Create a New Booking.",
                    ]);
                }
            }
        }
        return response()->json([
            "success" => true,
            "mesaage" => "Done",
        ]);
    }
    public function all_notifications(Request $request, $id)
    {
        $notifications = DB::table('firebase_notifications')
            ->where('user_id', $id)
            ->get();
        return response()->json([
            'notifications' => $notifications,
        ]);
    }
    public function notifybyfirebase($content, $content_en, $token, $data = [])
    {
        $title = "Imkan";
        $SERVER_API_KEY = "AAAAIgc53vk:APA91bG-b2eB7RaUcBe0YBjs9WEkzJzZqjsr9pSTiXcB1_sUebmOVakLEk_brHQFVwvbXW4eookrbExEFNLOIdJfzs1G7lrYCXbow-EOEtehPjJO10z6Qvq79NllBeIBMrPBBcd2bWwD";
        $data = [
            "registration_ids" => [
                $token
            ],
            "notification" => [
                "title" => $title,
                "body" => $content,
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
    }
    public function sendSmsEg($body, $phone)
    {
        $url = "https://smssmartegypt.com/sms/api/?username=abo.moomen87@gmail.com&password=E78179*uO&sendername=Shobeek&mobiles=" . $phone . "&message=" . urlencode($body);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
