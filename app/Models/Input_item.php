<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Input_item extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'code',
        'tradename',
        'common_name',
        'size',
        'unit_id',
        'pur_of_use',
        'RM_Group',

    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';


    public function unit()
    {
        return $this->hasOne(Units::class, 'id', 'unit_id');
    }

    public function tmp_schedule_plans()
    {
        return $this->hasMany(Tmp_schedule_plans::class);
    }
    public function plannings()
    {
        return $this->hasMany(Planning::class);
    }

    public function seed_codes()
    {
        return $this->hasMany(Seed_codes::class);
    }


}
