<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeItem extends Model
{
    protected $fillable = [
        'fee_id',
        'fee_category_id',
        'amount',
        'fine_amount',
        'total',
        'remarks',
        'source_type',
        'source_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }

    public function category()
    {
        return $this->belongsTo(
            FeeCategory::class,
            'fee_category_id'
        );
    }
}
