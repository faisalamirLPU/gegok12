<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Finance\BelongsToSchool;
use App\Traits\Finance\BelongsToAcademicYear;
use App\Traits\Finance\TracksUserActions;
class FeeCategory extends Model
{
    use SoftDeletes,
    BelongsToSchool,
    BelongsToAcademicYear,
    TracksUserActions;

    protected $fillable = [

        'school_id',
        'academic_year_id',

        'name',
        'code',
        'frequency',
        'description',

        'is_refundable',
        'is_optional',
        'status',

        'created_by',
        'updated_by',
    ];

    protected $casts = [

        'is_refundable' => 'boolean',
        'is_optional' => 'boolean',
        'status' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
