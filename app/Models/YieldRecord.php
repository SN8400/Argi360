<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YieldRecord extends Model
{
    use HasFactory;
    protected $table = 'yeilds';
    protected $fillable = [
        'crop_id', 'area_id', 'broker_id', 'input_item_id', 'harvest_type_id', 'start_date', 'end_date', 'rate', 'status', 'created', 'modified', 'kg_per_area'
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

    public function inputitem()
    {
        return $this->hasOne(Input_item::class, 'id', 'input_item_id');
    }

    public function harvesttype()
    {
        return $this->hasOne(Harvest_types::class, 'id', 'harvest_type_id');
    }

    public function generateNewSet($crop_id, $input_item_id, $areaList, $rate, $kgperarea = 13)
    {
        foreach ($areaList as $value) {

            $chk = self::where('crop_id', $crop_id)
                ->where('area_id', $value->area_id)
                ->where('broker_id', $value->broker_id)
                ->where('input_item_id', $input_item_id)
                ->count();

            if ($chk == 0) {
                $tmp = [];
                $tmp['crop_id'] = $crop_id;
                $tmp['area_id'] = $value->area_id;
                $tmp['broker_id'] = $value->broker_id;
                $tmp['input_item_id'] = $input_item_id;
                $tmp['rate'] = $rate;
                $tmp['kg_per_area'] = $kgperarea;

                self::create($tmp);
            }
        }
    }

    public function getYeildData($cropId, $area_id, $broker_id, $input_item_type, $date)
    {

        $data = self::where('crop_id', $cropId)
            ->where('area_id', $area_id)
            ->where('broker_id', $broker_id)
            ->where('input_item_id', $input_item_type)
            ->where('start_date','<=', $date)
            ->where('end_date', '>=', $date)
            ->first();

        if ($data->count() == 0) {

            $data = self::where('crop_id', $cropId)
            ->where('area_id', $area_id)
            ->where('broker_id', $broker_id)
            ->where('input_item_id', $input_item_type)
            ->first();

        }
        return $data;
    }
}
