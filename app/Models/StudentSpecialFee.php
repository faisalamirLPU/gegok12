<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Finance\Archivable;

class StudentSpecialFee extends Model
{
    use SoftDeletes,
        Archivable;

    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE = 1;
    public const STATUS_APPLIED = 2;
    public const STATUS_CANCELLED = 3;

    protected $fillable = [

        'school_id',

        'academic_year_id',

        'user_id',

        'fee_category_id',

        'amount',

        'due_date',

        'remarks',

        'status',
    ];

    protected $casts = [

        'amount' => 'decimal:2',

        'due_date' => 'date',

        'is_archived' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    public function feeCategory()
    {
        return $this->belongsTo(
            FeeCategory::class,
            'fee_category_id'
        );
    }
}
