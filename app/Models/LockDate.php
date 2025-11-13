<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LockDate extends Model
{
    use HasFactory;

    protected $table = 'lock_dates';

    protected $fillable = [
        'crop_id', 'type', 'start_date', 'end_date', 'status'
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function crop()
    {
        return $this->hasOne(Crops::class, 'id', 'crop_id');
    }
}
