<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Broker_areas extends Model
{
    use HasFactory;
    protected $fillable = [
        'crop_id',
        'broker_id',
        'area_id',
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function crop()
    {
        return $this->hasOne(Crops::class, 'id', 'crop_id');
    }

    public function broker()
    {
        return $this->hasOne(Brokers::class, 'id', 'broker_id');
    }
    public function area()
    {
        return $this->hasOne(Areas::class, 'id', 'area_id');
        // return $this->belongsTo(Areas::class, 'broker_id');
    }
}
