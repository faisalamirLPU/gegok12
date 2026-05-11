<?php
namespace App\Models;

use App\Traits\Finance\BelongsToAcademicYear;
use App\Traits\Finance\BelongsToSchool;
use App\Traits\Finance\TracksUserActions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeStructure extends Model
{
    // use SoftDeletes;
    use SoftDeletes,
    BelongsToSchool,
    BelongsToAcademicYear,
        TracksUserActions;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'class_id',
        'section_id',
        'title',
        'description',
        'installment_type',
        'due_type',
        'status',
        'created_by',
        'updated_by',
    ];

    public function assignments()
    {
        return $this->hasMany(
            StudentFeeAssignment::class,
            'fee_structure_id'
        );
    }

    protected $casts = [
        'status' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function items()
    {
        return $this->hasMany(FeeStructureItem::class);
    }

    public function class ()
    {
        return $this->belongsTo(Standard::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
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
        return $query->where('academic_year_id', $academicYearId);
    }
}
