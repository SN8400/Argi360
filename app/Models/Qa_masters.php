<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qa_masters extends Model
{
    use HasFactory;

    protected $fillable = [

        'crop_id',
        'qa_code',
        'plan_date',
        'receive_sample_date',
        'test_date',
        'result_date',
        'summary_result',
        'result_qa',
        'grade',
        'sample_no',
        'note',
        'test_no',
        'ref_id',
        'retest_flag',
        'loc_type',
        'createdBy',
        'modifiedBy',
        'created',
        'modified',
    ];

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function qa_group_details()
    {
        return $this->hasMany(Qa_group_details::class, 'id');
    }
}
