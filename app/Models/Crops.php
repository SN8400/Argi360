<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crops extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'details',
        'sap_code',
        'startdate',
        'enddate',
        'linkurl',
        'createdBy',
        'modifiedBy',
        'max_per_day',
    ];
    // ต้องเปลี่ยน timestamp
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';


    public function lockdate()
    {
        return $this->hasOne(LockDate::class, 'crop_id', 'id');
    }

    public function plannings()
    {
        return $this->hasMany(Planning::class,'crop_id','id');
    }
}
