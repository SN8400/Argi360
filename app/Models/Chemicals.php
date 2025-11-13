<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Standard_code;

class Chemicals extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'details',
        'formula_code',
        'standard_code_id',
        'unit_id',
        'rate_per_land',
        'bigunit_id',
        'package_per_bigunit',
        'ctype',
    ];

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function standardcode()
    {
        return $this->hasOne(Standard_code::class, 'id', 'standard_code_id');
    }

    public function unit()
    {
        return $this->hasOne(Units::class, 'id', 'unit_id');
    }

    public function bigunit()
    {
        return $this->hasOne(Units::class, 'id', 'bigunit_id');
    }

    public function tmp_schedule_plan_details()
    {
        return $this->hasMany(Tmp_schedule_plan_details::class, 'id');
    }
}
