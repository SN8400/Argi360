<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert_qs extends Model
{
    use HasFactory;

    protected $fillable = [
        'crop_id',
        'sowing_id',
        'plan_act_id',
        'user_act_id',
        'case_txt',
        'cond_range',
        'status',
        'created',
        'modified',
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}
