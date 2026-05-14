<?php

namespace App\Models;

use App\Traits\Finance\Archivable;
use App\Traits\Finance\BelongsToAcademicYear;
use App\Traits\Finance\BelongsToSchool;
use App\Traits\Finance\TracksUserActions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeStructure extends Model
{
    use SoftDeletes,
        Archivable,
        BelongsToSchool,
        BelongsToAcademicYear,
        TracksUserActions;

    protected $table = 'fee_structures';

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'class_id',
        'section_id',
        'title',
        'description',
        'installment_type',
        'due_type',
        'due_day',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_archived' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Structure Items
     */
    public function items()
    {
        return $this->hasMany(
            FeeStructureItem::class,
            'fee_structure_id'
        );
    }

    /**
     * Student Assignments
     */
    public function assignments()
    {
        return $this->hasMany(
            StudentFeeAssignment::class,
            'fee_structure_id'
        );
    }

    /**
     * Class / Standard
     */
    public function standard()
    {
        return $this->belongsTo(
            Standard::class,
            'class_id'
        );
    }

    /**
     * Alias for backward compatibility
     */
    public function class()
    {
        return $this->belongsTo(
            Standard::class,
            'class_id'
        );
    }

    /**
     * Section
     */
    public function section()
    {
        return $this->belongsTo(
            Section::class,
            'section_id'
        );
    }

    /**
     * Academic Year
     */
    public function academicYear()
    {
        return $this->belongsTo(
            AcademicYear::class,
            'academic_year_id'
        );
    }

    /**
     * School
     */
    public function school()
    {
        return $this->belongsTo(
            School::class,
            'school_id'
        );
    }

    /**
     * Creator
     */
    public function createdBy()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /**
     * Updater
     */
    public function updatedBy()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeBySchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeByAcademicYear($query, $academicYearId)
    {
        return $query->where(
            'academic_year_id',
            $academicYearId
        );
    }

    public function scopeLatestFirst($query)
    {
        return $query->latest();
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Full Class Name
     */
    public function getClassSectionAttribute()
    {
        $class = optional($this->standard)->name;
        $section = optional($this->section)->name;

        if ($class && $section) {
            return $class . ' - ' . $section;
        }

        if ($class) {
            return $class;
        }

        return 'Unassigned';
    }

    /**
     * Total Structure Amount
     */
    public function getTotalAmountAttribute()
    {
        return $this->items->sum('amount');
    }
}
