<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentFeeAssignment extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'school_id',

        'fee_id',

        'user_id',

        'academic_year_id',

        'standard_link_id',

        'assigned_amount',

        'paid_amount',

        'balance',

        'due_date',

        'assigned_on',

        'status',

        'created_by',

        'updated_by',
    ];

    protected $casts = [

        'assigned_amount' => 'decimal:2',

        'paid_amount' => 'decimal:2',

        'balance' => 'decimal:2',

        'due_date' => 'date',

        'assigned_on' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Student
    |--------------------------------------------------------------------------
    */

    public function student()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Student Profile
    |--------------------------------------------------------------------------
    */

    public function studentProfile()
    {
        return $this->hasOneThrough(
            Userprofile::class,
            User::class,
            'id',
            'user_id',
            'user_id',
            'id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Fee Structure
    |--------------------------------------------------------------------------
    */

    public function feeStructure()
    {
        return $this->belongsTo(
            FeeStructure::class,
            'fee_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Standard Link
    |--------------------------------------------------------------------------
    */

    public function standardLink()
    {
        return $this->belongsTo(
            StandardLink::class,
            'standard_link_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Fee Invoice
    |--------------------------------------------------------------------------
    */

    public function fee()
    {
        return $this->hasOne(
            Fee::class,
            'student_fee_assignment_id'
        );
    }
}
