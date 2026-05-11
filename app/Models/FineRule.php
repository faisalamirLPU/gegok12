<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Finance\BelongsToSchool;
use App\Traits\Finance\BelongsToAcademicYear;
use App\Traits\Finance\TracksUserActions;

class FineRule extends Model
{
    // use SoftDeletes;
    use SoftDeletes,
    BelongsToSchool,
    BelongsToAcademicYear,
    TracksUserActions;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'name',
        'fine_type',
        'amount',
        'grace_days',
        'max_limit',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'max_limit' => 'decimal:2',
        'status' => 'boolean'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function feeStructureItems()
    {
        return $this->hasMany(FeeStructureItem::class);
    }

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
