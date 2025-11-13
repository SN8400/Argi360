<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Import_qa_results extends Model
{
    use HasFactory;
    protected $fillable = [
        'filename',
        'row',
        'plant',
        'crop_code',
        'rm_group',
        'plan_no',
        'harvest_date',
        'sampling_date',
        'material_number',
        'material_description',
        'farmer_number',
        'farmer_name',
        'village',
        'broker_no',
        'broker_name',
        'er_name',
        'mng_er_name',
        'inspection_lot',
        'lot_ud',
        'system_status',
        'oper',
        'work_center',
        'point_no',
        'samping_no',
        'result_date',
        'point_ud',
        'item',
        'mic_description',
        'specification',
        'result',
        'uom',
        'created',
        'modified',
        'import_status',
        'sowing_id',
        'crop_id',
    ];

    const sowings_id = 'sowing_id';

    public function sowing()
    {
        return $this->belongsTo(Sowing::class, 'sowing_id', 'id');
    }
}
