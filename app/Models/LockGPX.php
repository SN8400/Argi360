<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LockGPX extends Model
{
    use HasFactory;
    
    protected $table = 'lock_gpx';

    protected $fillable = [
        'crop_id', 'dt_lock_gpx'
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function crop()
    {
        return $this->hasOne(Crops::class, 'id', 'crop_id');
    }
}
