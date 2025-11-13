<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanningDetail extends Model
{
    use HasFactory;
    protected $table = 'planning_details';
    protected $fillable =
    ['planning_id'
      ,'plan_date'
      ,'plan_value'
      ,'created'
      ,'modified'
      ,'area_id'
      ,'plan_yeild'
      ,'plan_areas'
      ,'harvest_age'
      ,'broker_id'
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function planning()
    {
        return $this->hasOne(Planning::class, 'id', 'planning_id');
    }

    public function broker()
    {
        return $this->hasOne(Brokers::class, 'id', 'broker_id');
    }

    public function area()
    {
        return $this->hasOne(Areas::class, 'id', 'area_id');
    }

    public function planningdetaildates()
    {
        return $this->hasMany(PlanningDetailDate::class, 'planning_detail_id', 'id');
    }

    public function yeilds()
    {
        return $this->hasManyThrough(YieldRecord::class, Planning::class, 'id', 'crop_id', 'planning_id', 'crop_id')
            ->whereColumn('yeilds.input_item_id', 'plannings.item_input_id')
            ->whereColumn('yeilds.harvest_type_id', 'plannings.harvest_type_id');
    }
    public function getFilteredYeildsAttribute()
    {
        return $this->yeilds->filter(function ($yeild) {
            return $yeild->area_id == $this->area_id && $yeild->broker_id == $this->broker_id;
        })->values();
    }

    public function generateNewSet($id, $areas)
    {
        foreach ($areas as $item) {
            $tmp = [];
            $tmp['planning_id'] = $id;
            $tmp['area_id'] = $item->id;
            $tmp['broker_id'] = $item->broker_id;

            $tmp['plan_value'] = 0;
            $tmp['plan_yeild'] = 0;
            $tmp['plan_areas'] = 0;

            $tmp['harvest_age'] = 68;
            self::create($tmp);
        }
    }
}
