<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

    protected $casts = [

        'payment_date' => 'datetime',
    ];
    protected $fillable = [

        'school_id',
        'academic_year_id',
        'fee_id',
        'user_id',
        'receipt_no',
        'amount',
        'payment_method',
        'transaction_id',
        'remarks',
        'payment_date',
    ];

    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }
}
