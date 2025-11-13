<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checklists extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'seq',
        'name',
        'name_eng',
        'desc',
        'status',
    ];
    // ต้องเปลี่ยน timestamp 
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}