<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tmp_schedule_plan_details extends Model
{
    use HasFactory;
    protected $fillable = [
        'tmp_schedule_plan_id',
        'chemical_id',
        'value',
        'unit_id',
        'ctype',
        'rate',
        'set_group',
        'p_value',
        'p_unit_id',

    ];

    const tmp_schedule_plans_id = 'tmp_schedule_plan_id';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
    public function tmp_schedule_plan()
    {
        return $this->belongsTo(Tmp_schedule_plans::class, 'tmp_schedule_plan_id');
    }

    public function chemical()
    {
        return $this->belongsTo(Chemicals::class, 'chemical_id', 'id');
    }

    public function unit()
    {
        return $this->hasOne(Units::class, 'id', 'unit_id');
    }

    public function punit()
    {
        return $this->hasOne(Units::class, 'id', 'p_unit_id');
    }

    public function group()
    {
        return $this->hasOne(Groups::class, 'id', 'set_group');
    }
    
}
