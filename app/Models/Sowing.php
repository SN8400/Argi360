<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sowing extends Model
{
    use HasFactory;
    protected $table = 'sowings';
    protected $fillable = [
        'crop_id', 'farmer_id', 'input_item_id',
        'harvest_type_id', 'land_size', 'item_value', 'gps_land', 'gps_seed', 'status', 'grow_rate', 'grow_date', 'grow_note', 'start_date',
        'details', 'relate', 'n_pos', 's_pos', 'e_pos', 'w_pos', 'createdBy', 'modifiedBy', 'created', 'modified', 'area_id', 'prev1', 'prev2', 'prev3',
        'head_id', 'user_id', 'current_land', 'fail_land', 'fail_date', 'fail_condition', 'yield_rate', 'name', 'amg_land', 'ph_water', 'ph_soy',
        'land_status', 'seed_code_id', 'seed_pack_id', 'harvest_status', 'seed_code_text', 'seed_code_date', 'farmer_status', 'raw_json', 'yield_rate7',
        'lat', 'lng', 'qa_status', 'qc_status', 'user_farmer_id', 'a2seed', 'a3seed', 'f_land', 'post_harvest_complain', 'post_harvest_qc', 'post_harvest_qa',
        'post_harvest_er', 'harvest_stage', 'check_current_date', 'n_pos_current', 's_pos_current', 'e_pos_current', 'w_pos_current', 'land_type', 'same_land',
        'qa_grade', 'qa_risk', 'seedperhole', 'gaphole', 'seedperhole2', 'gaphole2', 'area_type', 'groove_type'
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function crop()
    {
        return $this->hasOne(Crops::class, 'id', 'crop_id');
    }

    public function farmer()
    {
        return $this->hasOne(Farmers::class, 'id', 'farmer_id');
    }

    public function inputitem()
    {
        return $this->hasOne(Input_item::class, 'id', 'input_item_id');
    }

    public function harvesttype()
    {
        return $this->hasOne(Harvest_types::class, 'id', 'harvest_type_id');
    }

    public function harvestPlan()
    {
        return $this->hasOne(HarvestPlan::class, 'sowing_id', 'id');
    }

    public function npos()
    {
        return $this->hasOne(Plant_code::class, 'id', 'n_pos');
    }

    public function spos()
    {
        return $this->hasOne(Plant_code::class, 'id', 's_pos');
    }

    public function epos()
    {
        return $this->hasOne(Plant_code::class, 'id', 'e_pos');
    }

    public function wpos()
    {
        return $this->hasOne(Plant_code::class, 'id', 'w_pos');
    }

    public function area()
    {
        return $this->hasOne(Areas::class, 'id', 'area_id');
    }

    public function prev_1()
    {
        return $this->hasOne(Plant_code::class, 'id', 'prev1');
    }

    public function head()
    {
        return $this->hasOne(Heads::class, 'id', 'head_id');
    }

    public function ae()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function seedcode()
    {
        return $this->hasOne(Seed_codes::class, 'id', 'seed_code_id');
    }

    public function seedpack()
    {
        return $this->hasOne(SeedPack::class, 'id', 'seed_pack_id');
    }

    public function userfarmer()
    {
        return $this->hasOne(User_farmer::class, 'id', 'user_farmer_id');
    }

    public function qa_group_detail()
    {
        return $this->hasOne(Qa_group_details::class, 'sowing_id', 'id');
    }

    public function import_qa_results()
    {
        return $this->hasMany(Import_qa_results::class, 'id');
    }

    public function sample_plan()
    {
        return $this->hasOne(Sample_plans::class, 'sowing_id', 'id');
    }

    public function alert_q_details()
    {
        return $this->hasMany(Alert_q_details::class);
    }

    public function insect_act()
    {
        return $this->hasOne(Insect_acts::class, 'sowing_id', 'id');
    }
}
