<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanScheduleDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'plan_schedule_id',
        'chemical_id',
        'value',
        'unit_id',
        'p_value',
        'p_unit_id',
        'name',
        'rate',
        'ctype',
        'set_group',
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
    public function planSchedule()
    {
        return $this->belongsTo(PlanSchedule::class, 'plan_schedule_id');
    }

    public function chemical()
    {
        return $this->belongsTo(Chemicals::class, 'chemical_id', 'id');
    }
    
    public function unit()
    {
        return $this->belongsTo(Units::class, 'unit_id', 'id');
    }
    
    public function pUnit()
    {
        return $this->belongsTo(Units::class, 'p_unit_id', 'id');
    }
}
