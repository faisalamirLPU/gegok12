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
    ];

    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }
}
