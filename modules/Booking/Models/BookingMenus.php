<?php

namespace Modules\Booking\Models;

use App\BaseModel;

class BookingMenus extends BaseModel
{
    protected $table      = 'bravo_booking_menus';
    protected $fillable = ['menu_id', 'item_id', 'quantity', 'booking_id', 'tour_id'];
}
