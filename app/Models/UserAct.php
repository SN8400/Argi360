<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAct extends Model
{
    use HasFactory;
    protected $table = 'user_acts';
    protected $fillable = [
        'crop_id', 'plan_act_id', 'sowing_id', 'user_id', 'act_date', 'act_details', 'age_code', 'growing_rate', 'weed_rate', 'moisture_rate', 'disease1', 'disease2', 'disease3', 'disease4', 'disease5', 'disease6', 'disease7', 'disease8', 'disease9', 'disease_other', 'disease_other_text', 'insect1', 'insect2', 'insect3', 'insect4', 'insect5', 'insect6', 'insect7', 'insect8', 'insect9', 'insect_other', 'insect_other_text', 'harvest', 'labor', 'sorting', 'volumn', 'disguise', 'harvest_other', 'follow_up', 'created', 'modified', 'follow_case', 'attach_dir', 'attach', 'img_date', 'review_id', 'spray_flag', 'spray_note', 'spray_gallon', 'spray_bodo', 'spray_head', 'spray_machine', 'spray_team', 'disease_val1', 'disease_val2', 'disease_val3', 'disease_val4', 'disease_val5', 'disease_val6', 'disease_val7', 'disease_val8', 'disease_val9', 'disease_valother', 'insect_val1', 'insect_val2', 'insect_val3', 'insect_val4', 'insect_val5', 'insect_val6', 'insect_val7', 'insect_val8', 'insect_val9', 'insect_valother', 'alert_status', 'user_action_id', 'user_action_date', 'user_action_note', 'user_action_datetime'
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function crop()
    {
        return $this->hasOne(Crops::class, 'id', 'crop_id');
    }

    // public function planact()
    // {
    //     return $this->hasOne(PlanAct::class, 'id', 'plan_act_id');
    // }

    public function sowing()
    {
        return $this->hasOne(Sowing::class, 'id', 'sowing_id');
    }

    public function ae()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
