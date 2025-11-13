<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Areas extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name'
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function broker_areas()
    {
        return $this->hasMany(Broker_areas::class, 'broker_id');
    }
}
