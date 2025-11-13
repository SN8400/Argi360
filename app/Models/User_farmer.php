<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_farmer extends Model
{
    use HasFactory;
    protected $table = 'user_farmer';
    protected $fillable = [
        'crop_id',
        'user_id',
        'manager_id',
        'review_id',
        'broker_id',
        'area_id',
        'farmer_id',
        'head_id',
        'sowing_city',
        'farmer_code',
        'status',
        'createdBy',
        'modifiedBy',
        'created',
        'modified',
        'custom1',
        'custom2',
        'custom3',
        'custom4',
        'custom5',
    ];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function crop()
    {
        return $this->hasOne(Crops::class, 'id', 'crop_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function manager()
    {
        return $this->hasOne(User::class, 'id', 'manager_id');
    }

    public function review()
    {
        return $this->hasOne(User::class, 'id', 'review_id');
    }

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'createdBy');
    }

    public function modified_by()
    {
        return $this->hasOne(User::class, 'id', 'modifiedBy');
    }

    public function head()
    {
        return $this->hasOne(Heads::class, 'id', 'head_id');
    }

    public function broker()
    {
        return $this->hasOne(Brokers::class, 'id', 'broker_id');
    }
    public function area()
    {
        return $this->hasOne(Areas::class, 'id', 'area_id');
    }

    public function farmer()
    {
        return $this->hasOne(Farmers::class, 'id', 'farmer_id');
    }

    public function sowings()
    {
        return $this->hasMany(Sowing::class, 'user_farmer_id', 'id');
    }
}
