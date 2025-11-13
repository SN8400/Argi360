<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insect_acts extends Model
{
    use HasFactory;
    protected $fillable = [
        'crop_id',
        'sowing_id',
        'insect_name',
        'check_date',
        'age',
        'num_of_rdm_pod',
        'weight_of_rdm_pod',
        'num_of_found',
        'weight_of_found',
        'num_of_worm',
        'weight_of_worm',
        'note',
        'attach_dir1',
        'attach1',
        'attach_dir2',
        'attach2',
        'created',
        'modified',
    ];

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function crop()
    {
        return $this->hasOne(Crops::class, 'id', 'crop_id');
    }

    public function sowing()
    {
        return $this->hasOne(Sowing::class, 'id', 'sowing_id');
    }
}
