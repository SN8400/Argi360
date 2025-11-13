<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sub_input_items extends Model
{
    use HasFactory;
    protected $fillable = [
        'input_item_id',
        'code',
        'name',
        'mat_code_set',
        'note',
        'created_by',
        'modified_by',
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function user_create()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
    public function user_modified()
    {
        return $this->hasOne(User::class, 'id', 'modified_by');
    }

    public function inputitem()
    {
        return $this->hasOne(Input_item::class, 'id', 'input_item_id');
    }
}
