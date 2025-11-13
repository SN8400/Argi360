<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farmer_images extends Model
{
    use HasFactory;

    protected $fillable = [
        'crop_id',
        'farmer_id',
        'attach_dir',
        'attach',
        'created',
        'modified',
    ];

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

}
