<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentFeeLedger extends Model
{
    use SoftDeletes;

    protected $table = 'student_fee_ledger';

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'user_id',
        'transaction_type',
        'amount',
        'reference_type',
        'reference_id',
        'description',
        'payment_period',
        'balance_after',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Transaction Types
    |--------------------------------------------------------------------------
    */

    public const TYPE_CREDIT = 'credit';

    public const TYPE_DEBIT = 'debit';

    public const TYPE_CARRY_FORWARD = 'carry_forward';

    public const TYPE_ADVANCE_APPLIED = 'advance_applied';

    public const TYPE_ADVANCE_RECEIVED = 'advance_received';

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeForStudent($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForPeriod($query, string $paymentPeriod)
    {
        return $query->where('payment_period', $paymentPeriod);
    }

    public function scopeCredits($query)
    {
        return $query->whereIn('transaction_type', [
            self::TYPE_CREDIT,
            self::TYPE_ADVANCE_RECEIVED,
        ]);
    }

    public function scopeDebits($query)
    {
        return $query->whereIn('transaction_type', [
            self::TYPE_DEBIT,
            self::TYPE_ADVANCE_APPLIED,
        ]);
    }

    public function scopeAdvanceBalance($query)
    {
        return $query->whereIn('transaction_type', [
            self::TYPE_CREDIT,
            self::TYPE_ADVANCE_RECEIVED,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Advance Balance
    |--------------------------------------------------------------------------
    */

    public static function getAdvanceBalance(
        int $schoolId,
        int $userId
    ): float {

        $lastEntry = self::where('school_id', $schoolId)
            ->where('user_id', $userId)
            ->latest('id')
            ->first();

        return $lastEntry
            ? (float) $lastEntry->balance_after
            : 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Record Ledger Transaction
    |--------------------------------------------------------------------------
    */

    public static function recordTransaction(
        int $schoolId,
        int $academicYearId,
        int $userId,
        string $type,
        float $amount,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $description = null,
        ?string $paymentPeriod = null
    ): self {

        $currentBalance = self::getAdvanceBalance(
            $schoolId,
            $userId
        );

        switch ($type) {

            case self::TYPE_CREDIT:
            case self::TYPE_ADVANCE_RECEIVED:

                $newBalance = $currentBalance + $amount;

                break;

            case self::TYPE_DEBIT:
            case self::TYPE_ADVANCE_APPLIED:

                $newBalance = max(
                    0,
                    $currentBalance - $amount
                );

                break;

            case self::TYPE_CARRY_FORWARD:

                $newBalance = $amount;

                break;

            default:

                $newBalance = $currentBalance;
        }

        return self::create([
            'school_id' => $schoolId,

            'academic_year_id' => $academicYearId,

            'user_id' => $userId,

            'transaction_type' => $type,

            'amount' => $amount,

            'reference_type' => $referenceType,

            'reference_id' => $referenceId,

            'description' => $description,

            'payment_period' => $paymentPeriod,

            'balance_after' => $newBalance,
        ]);
    }
}