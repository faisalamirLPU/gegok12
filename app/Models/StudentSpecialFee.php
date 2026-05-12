<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentSpecialFee extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'school_id',

        'academic_year_id',

        'user_id',

        'fee_category_id',

        'amount',

        'due_date',

        'remarks',

        'status',
    ];

    protected $casts = [

        'amount' => 'decimal:2',

        'due_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    public function feeCategory()
    {
        return $this->belongsTo(
            FeeCategory::class,
            'fee_category_id'
        );
    }
}
