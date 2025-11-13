<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
    use HasFactory;
    protected $fillable = [
        'province_id',
        'th_name',
        'en_name'
    ];

    const provinces_id = 'province_id';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function province()
    {
        return $this->belongsTo(Provinces::class, 'province_id');
    }
}
