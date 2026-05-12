<?php
namespace App\Models;

use App\Models\StudentAcademic;
use App\Models\StudentFeeAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    protected $fillable = [
        'school_id',
        'academic_year_id',
        'student_fee_assignment_id',
        'student_academic_id',
        'user_id',
        'invoice_no',
        'billing_cycle',
        'total_amount',
        'paid_amount',
        'balance',
        'due_date',
        'generated_on',
        'status',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'due_date' => 'date',
        'generated_on' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(FeeItem::class);
    }

    public function student()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    public function studentAcademic()
    {
        return $this->belongsTo(
            StudentAcademic::class,
            'student_academic_id'
        );
    }

    public function studentAssignment()
    {
        return $this->belongsTo(
            StudentFeeAssignment::class,
            'student_fee_assignment_id'
        );
    }

    public function getBalanceAmountAttribute(): float
    {
        return max((float) $this->total_amount - (float) $this->paid_amount, 0);
    }

    public function getAdvanceAmountAttribute(): float
    {
        return max((float) $this->paid_amount - (float) $this->total_amount, 0);
    }

    public function getErpStatusAttribute(): string
    {
        if ($this->advance_amount > 0) {
            return 'Advance';
        }

        if ((float) $this->paid_amount >= (float) $this->total_amount) {
            return 'Paid';
        }

        if ((float) $this->paid_amount > 0) {
            return 'Partial';
        }

        return 'Pending';
    }
}
