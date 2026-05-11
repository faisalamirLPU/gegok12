<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    protected $fillable = [

        'school_id',
        'academic_year_id',
        'student_fee_assignment_id',
        'user_id',
        'invoice_no',
        'total_amount',
        'paid_amount',
        'balance',
        'due_date',
        'generated_on',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(FeeItem::class);
    }

    public function studentAssignment()
    {
        return $this->belongsTo(
            StudentFeeAssignment::class,
            'student_fee_assignment_id'
        );
    }
}
