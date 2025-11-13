<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HarvestMoveQ extends Model
{
    use HasFactory;
    protected $table = 'harvest_move_q';
    protected $fillable = [
        'crop_id',
        'harvest_plan_id',
        'sowing_id',
        'type',
        'to_date',
        'from_date',
        'age',
        'status',
        'mat_type',
        'havest_by',
        'value_est',
        'value_bf_harvest',
        'value_act',
        'note',
        'createdBy',
        'modifiedBy',
        'created',
        'modified',
    ];

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    // Relationships

    public function crop()
    {
        return $this->belongsTo(Crops::class, 'crop_id');
    }

    public function harvestPlan()
    {
        return $this->belongsTo(HarvestPlan::class, 'harvest_plan_id');
    }

    public function sowing()
    {
        return $this->belongsTo(Sowing::class, 'sowing_id');
    }   


}
