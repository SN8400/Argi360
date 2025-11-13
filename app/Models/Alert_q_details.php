<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert_q_details extends Model
{
    use HasFactory;

    protected $fillable = [
        'crop_id',
        'alert_q_id',
        'case_sowing_id',
        'sowing_id',
        'distance',
        'created',
        'modified',
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
    const sowings_id = 'sowing_id';


    public function sowing()
    {
        return $this->belongsTo(Sowing::class, 'sowing_id', 'id');
    }
}
