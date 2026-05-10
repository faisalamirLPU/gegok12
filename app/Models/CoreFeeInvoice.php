<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoreFeeInvoice extends Model
{
    protected $fillable = [
        'school_id',
        'core_fee_head_id',
        'student_id',
        'fee_month',
        'fee_year',
        'amount',
        'due_date',
        'status',
        'paid_at',
        'payment_mode',
        'transaction_reference',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function feeHead()
    {
        return $this->belongsTo(CoreFeeHead::class, 'core_fee_head_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
