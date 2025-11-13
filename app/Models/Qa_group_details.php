<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qa_group_details extends Model
{
    use HasFactory;
    protected $fillable = [
        'qa_master_id',
        'sowing_id',
        'sample_plan_id',
        'note',
        'status',
        'createdBy',
        'modifiedBy',
        'created',
        'modified',
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';


    public function sowing()
    {
        return $this->hasOne(Sowing::class, 'id', 'sowing_id');
        // return $this->hasMany(Sowing::class, 'sowing_id', 'id');
    }


    public function qa_master()
    {
        // return $this->belongsTo(Qa_masters::class,  'id', 'qa_master_id');
        return $this->hasOne(Qa_masters::class,  'id', 'qa_master_id');

    }
}
