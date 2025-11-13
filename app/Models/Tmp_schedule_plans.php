<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Tmp_schedule_plans extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'tmp_schedule_id',
        'input_item_id',
        'code',
        'main_code',
        'name',
        'details',
        'day',
        'priority',
        'can_review',
        'can_photo',
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function input_item()
    {
        return $this->belongsTo(Input_item::class, 'input_item_id', 'id');
    }
    public function tmp_schedules()
    {
        return $this->belongsTo(Tmp_schedules::class, 'tmp_schedule_id', 'id');
    }
    public function tmp_schedule_plan_details()
    {
        return $this->hasMany(Tmp_schedule_plan_details::class, 'tmp_schedule_plan_id');
    }
}
