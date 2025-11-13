<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanAct extends Model
{
    use HasFactory;
    protected $fillable = [
        'crop_id',
        'sowing_id',
        'plan_schedule_id',
        'plan_code',
        'plan_date',
        're_plan_date',
        'act_date',
        'spray_date',
        'note',
        'createdBy',
        'modifiedBy',
        'farmer_id',
        'priority',
        'age',
        'sample_status'
    ];
    
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
    
    public function planActDetails()
    {
        return $this->hasMany(PlanActDetail::class, 'plan_act_id', 'id');
    }
}
