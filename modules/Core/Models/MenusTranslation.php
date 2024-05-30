<?php
namespace Modules\Core\Models;

use App\BaseModel;

class MenusTranslation extends BaseModel
{
    protected $table = 'bravo_menus_translations';
    protected $fillable = [
        'name',
        'description',
    ];
}