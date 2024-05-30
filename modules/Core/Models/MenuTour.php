<?php

namespace Modules\Core\Models;

use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuTour extends BaseModel
{
    use SoftDeletes;
    protected $table = 'bravo_menus';
    protected $fillable = ['name', 'display_type', 'hide_in_single', 'hide_in_filter_search', 'position','price','category_id','parent_id','person_types','description',
        'check_maximum','extra_count'];
    protected $slugField = 'slug';
    protected $slugFromField = 'name';
    protected $casts = [
        'person_types'       => 'array',
    ];
    public function terms()
    {
        return $this->hasMany(Terms::class, 'menu_id', 'id')->with(['translation']);
    }

    public function fill(array $attributes)
    {
        if (!empty($attributes)) {
            foreach ($this->fillable as $item) {
                $attributes[$item] = $attributes[$item] ?? null;
            }
        }
        return parent::fill($attributes);
    }

    public static function getAllAttributesForApi($service_type)
    {
        $data = [];
        $attributes = Attributes::selectRaw("id,name,slug,service")->where('service', $service_type)->get();
        foreach ($attributes as $item) {
            $translation = $item->translate();
            $list_terms = $item->terms;
            $data[] = [
                'id'    => $item->id,
                'name'  => $translation->name,
                'slug'  => $item->slug,
                'terms' => $list_terms->map(function ($term) {
                    return $term->dataForApi();
                })
            ];
        }
        return $data;
    }
}
