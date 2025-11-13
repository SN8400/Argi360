<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Harvest_types;

class HarvestPlan extends Model
{
    use HasFactory;
    protected $table = 'harvest_plans';

    protected $fillable = [
        'crop_id',
        'area_id',
        'broker_id',
        'farmer_id',
        'sowing_id',
        'input_item_id',
        'user_id',
        'head_id',
        'seed_code_id',
        'age',
        'harvest_type',
        'havest_by',
        'ref_id',
        'mat_type',
        'delivery_status',
        'date_plan',
        'date_est',
        'date_act',
        'value_est',
        'value_bf_harvest',
        'value_act',
        'note',
        'reject_status',
        'reject_note',
        'qa_grade',
        'run_no',
        'selected_location',
        'createdBy',
        'modifiedBy',
        'created',
        'modified',
        'custommat'
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function crop()
    {
        return $this->hasOne(Crops::class, 'id', 'crop_id');
    }

    public function area()
    {
        return $this->hasOne(Areas::class, 'id', 'area_id');
    }

    public function broker()
    {
        return $this->hasOne(Brokers::class, 'id', 'broker_id');
    }

    public function farmer()
    {
        return $this->hasOne(Farmers::class, 'id', 'farmer_id');
    }

    public function sowing()
    {
        return $this->hasOne(Sowing::class, 'id', 'sowing_id');
    }

    public function inputitem()
    {
        return $this->hasOne(Input_item::class, 'id', 'input_item_id');
    }

    public function ae()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function head()
    {
        return $this->hasOne(Heads::class, 'id', 'head_id');
    }

    public function seedcode()
    {
        return $this->hasOne(Seed_codes::class, 'id', 'seed_code_id');
    }

    public function harvestType()
    {
        return $this->belongsTo(Harvest_types::class, 'harvest_type', 'id');
    }
        
    public function harvestMoveQ()
    {
        return $this->belongsTo(HarvestMoveQ::class, 'id', 'harvest_plan_id');
    }
}
