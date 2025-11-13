<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Harvest_types extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'note'
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

}