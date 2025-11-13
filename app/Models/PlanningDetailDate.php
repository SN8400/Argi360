<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanningDetailDate extends Model
{
    use HasFactory;
    protected $table = 'planning_detail_date';
    protected $fillable = [
        'crop_id'
      ,'version'
      ,'planning_detail_id'
      ,'plan_date'
      ,'plan_harvest'
      ,'age'
      ,'yeild'
      ,'plan_value'
      ,'status'
      ,'createdBy'
      ,'modifiedBy'
      ,'created'
      ,'modified'
      ,'yeild_ae'
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function crop()
    {
        return $this->hasOne(Crop::class, 'id', 'crop_id');
    }

    public function planningdetail()
    {
        return $this->hasOne(PlanningDetail::class, 'id', 'planning_detail_id');
    }

    public function setdisablecrop($crop_id, $version = "")
    {
        if (empty($version)) {
            self::where('crop_id', $crop_id)->update(['status'=> 0]);
            // $this->updateAll(array('PlanningDetailDate.status' => 0), array('PlanningDetailDate.crop_id' => $crop_id));
        } else {
            self::where('crop_id', $crop_id)->where('version', $version)->update(['status' => 0]);
            // $this->updateAll(array('PlanningDetailDate.status' => 0), array('PlanningDetailDate.crop_id' => $crop_id, 'PlanningDetailDate.version' => $version));
        }
    }
}
