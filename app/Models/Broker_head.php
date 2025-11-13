<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Broker_head extends Model
{
    use HasFactory;
    protected $table = 'broker_head'; 
    protected $fillable = [
        'crop_id',
        'broker_id',
        'head_id',
        'createdBy',
        'modifiedBy',

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
    public function head()
    {
        return $this->hasOne(Heads::class, 'id', 'head_id');
    }
}