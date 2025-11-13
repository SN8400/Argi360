<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanActDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'plan_act_id',
        'chemical_id',
        'value',
        'unit_id',
        'name',
        'rate',
        'ctype',
        'use_value',
        'note',
        'createdBy',
        'modifiedBy',
        'act_date',
        'cal_val',
        'use_status',
        'set_group'
    ];
    
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}
