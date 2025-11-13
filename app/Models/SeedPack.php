<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeedPack extends Model
{
    use HasFactory;
    protected $table = 'seed_packs';
    protected $fillable = [
        'seed_code_id', 'name', 'details', 'pack_date', 'status', 'created', 'modified'
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    const seed_codes_id = 'seed_code_id';

    public function seedcode()
    {
        return $this->belongsTo(Seed_codes::class, 'seed_code_id', 'id');
    }
}
