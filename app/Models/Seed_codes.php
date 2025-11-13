<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seed_codes extends Model
{
    use HasFactory;
    protected $fillable = [
        'crop_id',
        'input_item_id',
        'code',
        'details',
        'val_per_area',
        'seed_per_kg',
        'pack_date',
    ];

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';


    public function crop()
    {
        return $this->hasOne(Crops::class, 'id', 'crop_id');
    }

    public function input_item()
    {
        return $this->belongsTo(Input_item::class, 'input_item_id', 'id');
    }

    public function seed_packs()
    {
        return $this->hasMany(SeedPack::class, 'seed_code_id', 'id');
    }
}
