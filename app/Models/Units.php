<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Units extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'detail',
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function tmp_schedule_plan_details()
    {
        return $this->hasMany(Tmp_schedule_plan_details::class, 'id');
    }
}
