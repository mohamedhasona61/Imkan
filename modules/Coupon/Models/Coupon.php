<?php

namespace Modules\Coupon\Models;

use App\BaseModel;
use App\User;
use Illuminate\Support\Facades\Auth;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\Service;
use Modules\Tour\Models\Tour;

class Coupon extends BaseModel
{
    protected $table = 'bravo_coupons';
    protected $casts = [
        'services'      => 'array',
        'only_for_user'      => 'array',
    ];

    protected $bookingClass;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->bookingClass = Booking::class;
    }

    public function applyCoupon($booking, $action = 'add')
    {
        // Validate Coupon
        $res = $this->applyCouponValidate($booking, $action);
        if ($res !== true)
            return $res;
        switch ($action) {
            case "add":
                $this->add($booking);
                break;
            case "remove":
                $this->remove($booking);
                break;
        }
        return [
            'status' =>  1,
            'message' => __("Coupon code is applied successfully!")
        ];
    }

    public function applyCouponValidate($booking, $action)
    {
        if ($action == 'remove') {
            return true;
        }
        $check_coupon = CouponBookings::where('coupon_code', $this->code)->where("booking_id", $booking->id)->count();
        if (!empty($check_coupon)) {
            return [
                'status' => 0,
                'message' => __("Coupon code is added already!")
            ];
        }
        if (!empty($end_date = $this->end_date)) {
            $today = strtotime("today");
            if (strtotime($end_date) < $today) {
                return [
                    'status' => 0,
                    'message' => __("This coupon code has expired!")
                ];
            }
        }
        if (!empty($min_total = $this->min_total) and $booking->total_before_discount < $min_total) {
            return [
                'status' => 0,
                'message' => __("The order has not reached the minimum value of :amount to apply the coupon code!", ['amount' => format_money($min_total)])
            ];
        }
        if (!empty($max_total = $this->max_total) and $booking->total_before_discount > $max_total) {
            return [
                'status' => 0,
                'message' => __("This order has exceeded the maximum value of :amount to apply coupon code! ", ['amount' => format_money($max_total)])
            ];
        }
        if (!empty($this->services)) {
            $check = false;
            $service_booking_object_id = $booking->object_id;
            $service_booking_object_model = $booking->object_model;
            foreach ($this->couponServices as $item) {
                if ($item->object_id == $service_booking_object_id and $item->object_model == $service_booking_object_model) {
                    $check = true;
                }
            }
            if (!$check) {
                return [
                    'status' => 0,
                    'message' => __("Coupon code is not applied to this product!")
                ];
            }
        }
        if (!empty($this->only_for_user)) {
            if (empty($user_id = Auth::id())) {
                return [
                    'status' => 0,
                    'message' => __("You need to log in to use the coupon code!")
                ];
            }
            if (!in_array($user_id, $this->only_for_user)) {
                return [
                    'status' => 0,
                    'message' => __("Coupon code is not applied to your account!")
                ];
            }
        }
        if (!empty($quantity_limit = $this->quantity_limit)) {
            $count = CouponBookings::where('coupon_code', $this->code)->whereNotIn('booking_status', ['draft', 'unpaid', 'cancelled'])->count();
            if ($quantity_limit <= $count) {
                return [
                    'status' => 0,
                    'message' => __("This coupon code has been used up!")
                ];
            }
        }
        if (!empty($limit_per_user = $this->limit_per_user)) {
            if (empty($user_id = Auth::id())) {
                return [
                    'status' => 0,
                    'message' => __("You need to log in to use the coupon code!")
                ];
            }
            $count = CouponBookings::where('coupon_code', $this->code)->where("create_user", $user_id)->whereNotIn('booking_status', ['draft', 'unpaid', 'cancelled'])->count();
            if ($limit_per_user <= $count) {
                return [
                    'status' => 0,
                    'message' => __("This coupon code has been used up!")
                ];
            }
        }
        return true;
    }

    public function remove($booking)
    {
        $couponBooking = CouponBookings::where('coupon_code', $this->code)->where('booking_id', $booking->id)->first();
        if (!empty($couponBooking)) {
            $couponBooking->delete();
        }
        if ($booking->service->enable_service_fee == 1) {
            $Tour = Tour::find($booking->object_id);
            $booking->total = $booking->total_before_discount;
            $booking->total_before_fees =  $booking->coupon_amount + $booking->total_before_fees;
            $booking->vendor_service_fee_amount = $booking->total_before_fees / 100 * $Tour->service_fee[0]['price'];
            $booking->coupon_amount = 0;

            $booking->save();
        } else {
            $booking->total = $booking->total_before_discount;
            $booking->total_before_fees =  $booking->coupon_amount + $booking->total_before_fees;
            $booking->vendor_service_fee_amount = 0;
            $booking->coupon_amount = 0;
            $booking->save();
        }
        $booking->vendor_service_fee_amount = $booking->total  - $booking->total_before_fees;
        $booking->deposit = $booking->total / 2;
        $booking->save();
    }

    public function add($booking)
    {
        //for Type Fixed
        $coupon_amount = $this->amount;
        //for Type Percent
        if ($this->discount_type == 'percent') {


            $coupon_amount =  $booking->total_before_fees / 100 * $this->amount;
        }
        $couponBooking = new CouponBookings();
        $couponBooking->fill([
            'booking_id' => $booking->id,
            'booking_status' => $booking->status,
            'object_id' => $booking->object_id,
            'object_model' => $booking->object_model,
            'coupon_code' => $this->code,
            'coupon_amount' => $coupon_amount,
            'coupon_data' => $this->toArray(),
        ]);
        $couponBooking->save();

        $coupon_amount  = CouponBookings::where('booking_id', $booking->id)->sum('coupon_amount');
        $total_after_update = $booking->total_before_fees - $coupon_amount;
        $fee_after_update = 0;
        if ($booking->service->enable_service_fee == 1) {
            $Tour = Tour::find($booking->object_id);
            $item = $Tour->service_fee;
            $fee_after_update = ($total_after_update / 100) *  $Tour->service_fee[0]['price'];
        }
        $booking->total =  $total_after_update + $fee_after_update;
        $booking->total_before_fees =  $total_after_update;
        $booking->deposit = ($total_after_update + $fee_after_update) / 2;
        $booking->vendor_service_fee_amount =  $fee_after_update;
        $booking->coupon_amount =  $coupon_amount;

        $booking->save();
    }

    public function couponServices()
    {
        return $this->hasMany(CouponServices::class, 'coupon_id');
    }
    public function getServicesToArray()
    {
        $data = [];
        if (!empty($this->services)) {
            $services = Service::selectRaw('id,object_id,object_model,title')->whereIn('id', $this->services)->get();
            foreach ($services as $item) {
                $data[] = [
                    'id'   => $item->id,
                    'text' => strtoupper($item->object_model) . " (#{$item->object_id}): {$item->title}"
                ];
            }
        }
        return $data;
    }
    public function getUsersToArray()
    {
        $data = [];
        if (!empty($this->only_for_user)) {
            $users = User::where('status', 'publish')->whereIn('id', $this->only_for_user)->get();
            foreach ($users as $item) {
                $data[] = [
                    'id'   => $item->id,
                    'text' => "(#{$item->id}): {$item->getDisplayName()}"
                ];
            }
        }
        return $data;
    }
}
