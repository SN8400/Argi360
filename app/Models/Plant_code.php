<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plant_code extends Model
{
    use HasFactory;
    protected $table = 'plant_code'; // default model call "plant_codes"
    protected $fillable = [
        'code',
        'name',
        'details'
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}