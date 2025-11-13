<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planning extends Model
{
    use HasFactory;
    protected $fillable = [
        'crop_id',
        'item_input_id',
        'harvest_type_id',
        'plan_date',
        'plan_value',
        'plan_kg_area',
        'created', 'modified', 'max_per_day', 'start_sowing'
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function crop()
    {
        return $this->belongsTo(Crops::class, 'crop_id', 'id');
    }

    public function inputitem()
    {
        return $this->hasOne(Input_item::class, 'id', 'item_input_id');
    }

    public function harvesttype()
    {
        return $this->hasOne(Harvest_types::class, 'id', 'harvest_type_id');
    }

    public function planningdetails()
    {
        return $this->hasMany(PlanningDetail::class, 'planning_id', 'id');
    }
}
