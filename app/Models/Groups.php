<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groups extends Model
{
    use HasFactory;
    protected $fillable = [
        'name'
    ];
    const CREATED_AT = 'created'; 
    const UPDATED_AT = 'modified';
}
