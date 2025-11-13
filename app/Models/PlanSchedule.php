<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'crop_id',
        'input_item_id',
        'code',
        'main_code',
        'name',
        'gapdata',
        'details',
        'day',
        'area_id',
        'priority',
        'broker_id',
        'can_review',
        'can_photo',
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function crop()
    {
        return $this->hasOne(Crops::class, 'id', 'crop_id');
    }

    public function broker()
    {
        return $this->hasOne(Brokers::class, 'id', 'broker_id');
    }

    public function input_item()
    {
        return $this->hasOne(Input_item::class, 'id', 'input_item_id');
    }

    public function area()
    {
        return $this->hasOne(Areas::class, 'id', 'area_id');
    }

    public function details()
    {
        return $this->hasMany(PlanScheduleDetail::class, 'plan_schedule_id', 'id');
    }

}
