<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportQcSAP extends Model
{
    use HasFactory;
    protected $table = 'import_qc_saps';

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
        'sampling_plan',
        'farmer_no',
        'inspection_lot',
        'material',
        'broker_id',
        'broker_run',
        'car_run',
        'num_defect',
        'worm_crept_pod',
        'wormy_pods',
        'destroy_from_insect',
        'rotten_pod',
        'antracnose',
        'downy_mildew',
        'others_disease',
        'scar',
        'scar_from_disease',
        'brown_pod',
        'purple_pod',
        'one_seed_pod',
        'hin_pod',
        'one_seed_missing_half',
        'short_pod',
        'missshape',
        'yellowish',
        'bruise',
        'string_off',
        'dirty_frm_soil',
        'dirty_frm_sand',
        'stem_leave',
        'shrink_pod',
        'damage_harvester',
        'other_wast',
        'formula_1',
        'total_defect_sum_perc',
        'total_defect_g',
        'total_defect_cal_perc',
        'pod_per_500g',
        'lower3cm',
        'more5cm',
        'defect_grade',
        'sample_worm_g',
        'worm_qty',
        'worm_wg_g',
        'worm_cal_perc',
        'wormy_qty',
        'wormy_wg_g',
        'wormy_cal_perc',
        'total_worm_sum_perc',
        'damage_by_worm_qty',
        'damage_by_worm_wg_g',
        'damage_worm_cal_perc',
        'brown_pod_qty',
        'brown_pod_wg_g',
        'brown_pod_perc',
        'total_defect_sum_perc2',
        'worm_code',
        'car_tube',
        'tent_cover',
        'basket_broken',
        'pesticide_japan',
        'status_pesticide',
        'usage_decision'
    ];

    public $timestamps = false;

    // Relationships (optional, add if needed)
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

    public function broker()
    {
        return $this->belongsTo(Brokers::class, 'broker_id');
    }
}
