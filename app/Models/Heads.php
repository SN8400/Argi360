<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Heads extends Model
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
}