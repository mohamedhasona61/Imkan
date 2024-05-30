<?php

namespace Modules\Tour\Models;

use App\BaseModel;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryTimeSlot extends BaseModel
{

    protected $table = 'category_time_slots';
    protected $fillable = [
        'category_id',
        'start_at',
        'end_at',
        'active',
        'day',
    ];



    public function category()
    {
        return $this->belongsTo(TourCategory::class);
    }
}
