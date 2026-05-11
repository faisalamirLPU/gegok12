<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Finance\BelongsToSchool;
use App\Traits\Finance\BelongsToAcademicYear;
use App\Traits\Finance\TracksUserActions;

class Concession extends Model
{
    // use SoftDeletes;
    use SoftDeletes,
    BelongsToSchool,
    BelongsToAcademicYear,
    TracksUserActions;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'student_id',
        'type',
        'amount',
        'reason',
        'approved_by',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
