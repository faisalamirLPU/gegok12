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
        'month',
        'year',
        'payment_period',
        'total_amount',
        'paid_amount',
        'balance',
        'due_date',
        'generated_on',
        'status',
        'is_locked',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'due_date' => 'date',
        'generated_on' => 'date',
        'is_locked' => 'boolean',
    ];

    // Status constants
    const STATUS_PENDING = 0;
    const STATUS_PARTIAL = 1;
    const STATUS_PAID = 2;
    const STATUS_ADVANCE = 3;

    public function items()
    {
        return $this->hasMany(FeeItem::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function studentAcademic()
    {
        return $this->belongsTo(StudentAcademic::class, 'student_academic_id');
    }

    public function studentAssignment()
    {
        return $this->belongsTo(StudentFeeAssignment::class, 'student_fee_assignment_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getBalanceAmountAttribute(): float
    {
        return max((float) $this->total_amount - (float) $this->paid_amount, 0);
    }

    public function getAdvanceAmountAttribute(): float
    {
        return max((float) $this->paid_amount - (float) $this->total_amount, 0);
    }

    public function getAdvanceCreditAttribute(): float
    {
        return max((float) $this->paid_amount - (float) $this->total_amount, 0);
    }

    public function getErpStatusAttribute(): string
    {
        if ($this->advance_amount > 0) {
            return 'Advance';
        }

        if ((float) $this->paid_amount >= (float) $this->total_amount && (float) $this->total_amount > 0) {
            return 'Paid';
        }

        if ((float) $this->paid_amount > 0) {
            return 'Partial';
        }

        return 'Pending';
    }

    public function getErpStatusBadgeClassAttribute(): string
    {
        return match ($this->erp_status) {
            'Advance' => 'bg-blue-100 text-blue-700 border border-blue-200',
            'Paid' => 'bg-green-100 text-green-700 border border-green-200',
            'Partial' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
            'Pending' => 'bg-red-100 text-red-700 border border-red-200',
            default => 'bg-gray-100 text-gray-700 border border-gray-200',
        };
    }

    public function getMonthNameAttribute(): string
    {
        if (!$this->month) {
            return '-';
        }
        return \Carbon\Carbon::createFromDate($this->year ?? now()->year, $this->month, 1)->format('F');
    }

    public function getDisplayPeriodAttribute(): string
    {
        if ($this->month && $this->year) {
            return \Carbon\Carbon::createFromDate($this->year, $this->month, 1)->format('F Y');
        }
        return $this->billing_cycle ?? '-';
    }

    public function scopeForMonth($query, int $month)
    {
        return $query->where('month', $month);
    }

    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    public function scopeForPeriod($query, string $period)
    {
        return $query->where('payment_period', $period);
    }

    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }

    public function scopeLocked($query)
    {
        return $query->where('is_locked', true);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID)
            ->orWhere(function ($q) {
                $q->whereColumn('paid_amount', '>=', 'total_amount')
                  ->where('total_amount', '>', 0);
            });
    }

    public function scopePartial($query)
    {
        return $query->where('status', self::STATUS_PARTIAL)
            ->orWhere(function ($q) {
                $q->where('paid_amount', '>', 0)
                  ->whereColumn('paid_amount', '<', 'total_amount');
            });
    }

    public function scopeWithAdvance($query)
    {
        return $query->where('status', self::STATUS_ADVANCE)
            ->orWhere(function ($q) {
                $q->whereColumn('paid_amount', '>', 'total_amount');
            });
    }

    public function lock(): bool
    {
        return $this->update(['is_locked' => true]);
    }

    public function unlock(): bool
    {
        return $this->update(['is_locked' => false]);
    }

    public function recalculateStatus(): self
    {
        $this->refresh();

        $balance = (float) $this->balance;
        $advanceAmount = (float) $this->advance_amount;
        $paidAmount = (float) $this->paid_amount;
        $totalAmount = (float) $this->total_amount;

        $oldStatus = $this->status;
        $newStatus = self::STATUS_PENDING;

        if ($balance <= 0 && $advanceAmount > 0) {
            $newStatus = self::STATUS_ADVANCE;
        } elseif ($balance <= 0 && $totalAmount > 0) {
            $newStatus = self::STATUS_PAID;
        } elseif ($paidAmount > 0) {
            $newStatus = self::STATUS_PARTIAL;
        }

        $this->update(['status' => $newStatus]);

        \Log::info("ERP Invoice Status Sync: [Inv: {$this->id}] [Old Status: {$oldStatus}] [New Status: {$newStatus}] [Balance: {$balance}] [Paid: {$paidAmount}] [Advance: {$advanceAmount}]");

        return $this;
    }

    public function recalculateTotals(): self
    {
        $total = (float) $this->items()->sum('total');
        $paid = (float) $this->paid_amount;
        
        $balance = max($total - $paid, 0);
        $advance = max($paid - $total, 0);

        $this->update([
            'total_amount' => $total,
            'balance' => $balance,
            'advance_amount' => $advance,
        ]);

        return $this->recalculateStatus();
    }
}
