<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checklist_crops extends Model
{
    use HasFactory;
    protected $fillable = [
        'crop_id',
        'checklist_id',
        'conds',
        'unit',
        'field_map_result',
        'field_map_val',
        'desc',
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function crop()
    {
        return $this->hasOne(Crops::class, 'id', 'crop_id');
    }

    public function checklist()
    {
        return $this->hasOne(Checklists::class, 'id', 'checklist_id');
    }

    public function copynewcrop($fromCrop, $toCrop, $created, $modified)
    {
        $originalData = Checklist_crops::where('crop_id', intval($fromCrop))->get();

        if ($originalData->count() > 0) {
            $checkTarget = Checklist_crops::where('crop_id', intval($toCrop))->count();

            if ($checkTarget == 0) {
                $allGrowStates = [];
                foreach ($originalData as $originalObj) {
                    $tmp = [];
                    $tmp['crop_id'] = intval($toCrop);

                    $tmp['checklist_id'] = $originalObj->checklist_id;
                    $tmp['conds'] = $originalObj->conds;
                    $tmp['unit'] = $originalObj->unit;
                    $tmp['field_map_result'] = $originalObj->field_map_result;
                    $tmp['field_map_val'] = $originalObj->field_map_val;
                    $tmp['desc'] = $originalObj->desc;


                    $tmp['created'] = $created;
                    $tmp['modified'] = $modified;

                    $allGrowStates[] = $tmp;

                }

                Checklist_crops::insert($allGrowStates);

                return "complete";
            } else {
                return "duplicate";
            }
        } else {
            printf($originalData);
            return "nodata";
        }
    }
}