<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sample_plans extends Model
{
    use HasFactory;

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
        'date_plan',
        'date_harvest',
        'date_est',
        'date_act',
        'land_value',
        'value_est',
        'value_bf_harvest',
        'value_act',
        'marked',
        'sample_qa',
        'sample_qc',
        'qa_value',
        'qc_value',
        'qa_recv',
        'qc_recv',
        'qc_result_value',
        'qc_result_txt',
        'qa_status',
        'qc_status',
        'note',
        'createdBy',
        'modifiedBy',
        'created',
        'modified',
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}
