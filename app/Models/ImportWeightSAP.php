<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportWeightSAP extends Model
{
    use HasFactory;

    protected $table = 'import_weight_saps';

    protected $fillable = [
        'file_name',
        'line',
        'mapstatus',
        'crop_id',
        'harvest_plan_id',
        'sowing_id',
        'wg_plant',
        'crop',
        'rm_group',
        'wg_type',
        'wg_doc',
        'wg_item',
        'wg_date',
        'wg_time',
        'harvest_date',
        'aging',
        'car_plate',
        'total_wg',
        'pkg_qty',
        'wg_pkg',
        'net_wg',
        'sampling_plan',
        'farmer_no',
        'lot_beforcrop',
        'ud_beforecrop',
        'lot_start_date',
        'lot_end_date',
        'inspection_type',
        'inspection_lot',
        'material',
        'mat_des',
        'plant',
        'batch',
        'lot_qty',
        'post_to_ur',
        'post_to_block',
        'uom',
        'lot_short_test',
        'system_status',
        'lot_ud',
        'plan_des',
        'oper10',
        'oper20',
        'oper30',
        'oper40',
        'oper50',
        'oper60',
        'status_qc',
        'status_physical',
        'status_pesticide',
        'status_micro',
        'status_sensory',
        'status_usage',
        'pallet_id',
    ];

    public $timestamps = false;

    // ความสัมพันธ์กับ model อื่น ๆ
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
