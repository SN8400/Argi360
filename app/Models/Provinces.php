<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provinces extends Model
{
    use HasFactory;
    protected $fillable = [
        'th_name',
        'en_name',
    ];

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function cities()
    {
        return $this->hasMany(Cities::class, 'province_id');
    }
}
