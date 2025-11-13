<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Map_matcode extends Model
{
    use HasFactory;

    protected $table = 'map_matcode';
    protected $fillable = [
        'crop_id',
        'input_item_id',
        'broker_id',
        'harvest_by',
        'harvest_to',
        'matcode',
        'desc',
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function crop()
    {
        return $this->hasOne(Crops::class, 'id', 'crop_id');
    }

    public function inputitem()
    {
        return $this->hasOne(Input_item::class, 'id', 'input_item_id');
    }
    public function broker()
    {
        return $this->hasOne(Brokers::class, 'id', 'broker_id');
    }

    public function copynewcrop($fromCrop, $from_broker_id, $toCrop, $to_broker_id, $created, $modified)
    {
        $originalData = Map_matcode::where('crop_id', intval($fromCrop))
                        ->where('broker_id', intval($from_broker_id))
                        ->get();

        if ($originalData->count() > 0) {

            foreach ($originalData as $map) {
                $existing = Map_matcode::where('crop_id', $toCrop)
                    ->where('broker_id', $to_broker_id)
                    ->where('input_item_id', $map->input_item_id)
                    ->where('harvest_by', $map->harvest_by)
                    ->where('harvest_to', $map->harvest_to)
                    ->first();

                if (!$existing) {
                    // Create new
                    Map_matcode::create([
                        'crop_id' => $toCrop,
                        'broker_id' => $to_broker_id,
                        'input_item_id' => $map->input_item_id,
                        'harvest_by' => $map->harvest_by,
                        'harvest_to' => $map->harvest_to,
                        'matcode' => $map->matcode,
                        'desc' => $map->desc,
                        'created_at' => $created,
                        'updated_at' => $modified
                    ]);
                } else {
                    // Update existing
                    $existing->update([
                        'matcode' => $map->matcode,
                        'desc' => $map->desc,
                        'updated_at' => $modified
                    ]);
                }
            }
                    

        //     if ($checkTarget == 0) {
        //         $allGrowStates = [];
        //         foreach ($originalData as $originalObj) {
        //             $tmp = [];
        //             $tmp['crop_id'] = intval($toCrop);
        //             $tmp['input_item_id'] = $toInput;

        //             $tmp['broker_id'] = $originalObj->broker_id;
        //             $tmp['harvest_by'] = $originalObj->harvest_by;
        //             $tmp['harvest_to'] = $originalObj->harvest_to;
        //             $tmp['matcode'] = $originalObj->matcode;
        //             $tmp['desc'] = $originalObj->desc;
                

        //             $tmp['created'] = $created;
        //             $tmp['modified'] = $modified;

        //             $allGrowStates[] = $tmp;

        //         }

        //         Map_matcode::insert($allGrowStates);

                return "complete";
        //     } else {
        //         return "duplicate";
        //     }
        } else {
            printf($originalData);
            return "nodata";
        }
    }

}