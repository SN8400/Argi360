<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farmers extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'init',
        'fname',
        'lname',
        'citizenid',
        'address1',
        'address2',
        'address3',
        'sub_cities',
        'city_id',
        'province_id',
        'createdBy',
        'modifiedBy',
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function province()
    {
        return $this->hasOne(Provinces::class, 'id', 'province_id');
    }
    public function city()
    {
        return $this->hasOne(Cities::class, 'id', 'city_id');
    }

    public function farmer_card()
    {
        return $this->hasOne(Farmer_cards::class, 'farmer_id', 'id');
    }

    public function farmer_image()
    {
        return $this->hasOne(Farmer_images::class, 'farmer_id', 'id')->latest('modified');;
    }
}
