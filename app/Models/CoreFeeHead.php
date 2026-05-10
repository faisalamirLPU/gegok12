<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoreFeeHead extends Model
{
    protected $fillable = [
        'school_id',
        'name',
        'fee_type',
        'amount',
        'due_day',
        'is_active',
    ];

    public function invoices()
    {
        return $this->hasMany(CoreFeeInvoice::class);
    }
}
