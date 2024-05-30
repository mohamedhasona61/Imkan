<?php

namespace Modules\Tour\Models;

use App\BaseModel;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bookning_dates extends BaseModel
{

    protected $table = 'booking_dates';
    protected $fillable = [
        'category_id',
        'tour_id',
        'day',
        'time_slot',
        'day',
        'create_user',
        'update_user',
        'day',
        'active',
    ];


}
