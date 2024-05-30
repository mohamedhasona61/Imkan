<?php
namespace Modules\Core\Models;

use App\BaseModel;

class ExtrasTranslation extends BaseModel
{
    protected $table = 'bravo_extras_translations';
    protected $fillable = [
        'name',
    ];
}