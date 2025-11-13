<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Standard_code extends Model
{
    use HasFactory;

    protected $table = "standard_code";
    protected $fillable = [
        'standard_name',
        'details',
        'chemical_type',
        'MRLs',
        'major_type',
        'type_code',
        'rate',
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}