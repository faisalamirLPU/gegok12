<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeStructureItem extends Model
{
    use SoftDeletes;

        protected $fillable = [

        'school_id',
        'academic_year_id',

        'fee_structure_id',

        'fee_category_id',

        'fine_rule_id',

        'amount',

        'due_date',

        'is_optional',

        'sort_order',

        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_optional' => 'boolean',
        'due_date' => 'date'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function feeCategory()
    {
        return $this->belongsTo(FeeCategory::class);
    }

    public function fineRule()
    {
        return $this->belongsTo(FineRule::class);
    }
}
