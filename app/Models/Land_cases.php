<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Land_cases extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'details'
    ];

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

}