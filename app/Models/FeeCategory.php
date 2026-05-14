<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Finance\Archivable;
use App\Traits\Finance\BelongsToSchool;
use App\Traits\Finance\BelongsToAcademicYear;
use App\Traits\Finance\TracksUserActions;

class FeeCategory extends Model
{
    use SoftDeletes,
        Archivable,
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
        'is_archived' => 'boolean',
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

    public function feeItems()
    {
        return $this->hasMany(
            FeeItem::class,
            'fee_category_id'
        );
    }

    public function structureItems()
    {
        return $this->hasMany(
            FeeStructureItem::class,
            'fee_category_id'
        );
    }

    public function studentSpecialFees()
    {
        return $this->hasMany(
            StudentSpecialFee::class,
            'fee_category_id'
        );
    }
}
