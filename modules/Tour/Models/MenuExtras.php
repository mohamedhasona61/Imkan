<?php

namespace Modules\Tour\Models;

use App\BaseModel;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\MenuTour;

class MenuExtras extends BaseModel
{

    protected $table = 'menu_extras';
    protected $fillable = [
        'menu_id',
        'name',
    ];



    public function menu()
    {
        return $this->belongsTo(MenuTour::class);
    }
}
