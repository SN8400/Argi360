<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Grow_states extends Model
{
    use HasFactory;
    protected $fillable = [
        'crop_id',
        'input_item_id',
        'code',
        'name',
        'age',
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function crop()
    {
        return $this->belongsTo(Crops::class, 'crop_id', 'id'); // ✅ แก้ตรงนี้
    }

    public function input_item()
    {
        return $this->belongsTo(Input_item::class, 'input_item_id', 'id'); // ✅ แก้ตรงนี้
    }
    public function copynewcrop($fromCrop, $fromInput, $toCrop, $toInput, $created, $modified)
    {
        $originalData = Grow_states::where('crop_id', intval($fromCrop))->where('input_item_id', intval($fromInput))->get();
        if ($originalData->count() > 0) {
            $checkTarget = Grow_states::where('crop_id', intval($toCrop))->where('input_item_id', intval($toInput))->count();
            if ($checkTarget == 0) {
                $allGrowStates = [];
                foreach ($originalData as $originalObj) {
                    $tmp = [];
                    $tmp['crop_id'] = intval($toCrop);
                    $tmp['input_item_id'] = $toInput;
                    $tmp['code'] = $originalObj->code;
                    $tmp['name'] = $originalObj->name;
                    $tmp['age'] = $originalObj->age;
                    $tmp['created'] = $created;
                    $tmp['modified'] = $modified;
                    $allGrowStates[] = $tmp;
                }
                
                $chunks = array_chunk($allGrowStates, 50);

                foreach ($chunks as $chunk) {
                    Grow_states::insert($chunk);
                }
                // Grow_states::insert($allGrowStates);

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