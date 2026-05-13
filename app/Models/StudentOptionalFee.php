<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentOptionalFee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'fee_category_id',
        'school_id',
        'academic_year_id',
        'is_selected',
    ];

    protected $casts = [
        'is_selected' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function feeCategory()
    {
        return $this->belongsTo(FeeCategory::class, 'fee_category_id');
    }

    public static function isSelected(int $userId, int $categoryId, int $academicYearId): bool
    {
        return self::where('user_id', $userId)
            ->where('fee_category_id', $categoryId)
            ->where('academic_year_id', $academicYearId)
            ->where('is_selected', true)
            ->exists();
    }

    public static function toggle(int $userId, int $categoryId, int $schoolId, int $academicYearId): bool
    {
        $existing = self::where('user_id', $userId)
            ->where('fee_category_id', $categoryId)
            ->where('academic_year_id', $academicYearId)
            ->first();

        if ($existing) {
            $existing->update(['is_selected' => !$existing->is_selected]);
            return $existing->fresh()->is_selected;
        }

        self::create([
            'user_id' => $userId,
            'fee_category_id' => $categoryId,
            'school_id' => $schoolId,
            'academic_year_id' => $academicYearId,
            'is_selected' => true,
        ]);

        return true;
    }
}