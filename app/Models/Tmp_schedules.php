<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tmp_schedules extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'details',
        'status',
        'broker_id',
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function tmp_schedule_plans()
    {
        return $this->hasMany(Tmp_schedule_plans::class, 'tmp_schedule_id');
    }

    public function broker()
    {

        return $this->hasOne(Brokers::class, 'id', 'broker_id');
    }

    public function plans()
    {
        return $this->hasMany(Tmp_schedule_plans::class, 'tmp_schedule_id');
    }
}
