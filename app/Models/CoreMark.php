<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoreMark extends Model
{
    protected $fillable = [
        'core_exam_id',
        'core_exam_subject_id',
        'student_id',

        'marks_obtained',

        'attendance_status',

        'grade',

        'is_passed',

        'remarks',

        'checked_by',

        'checked_at',
    ];

    protected $casts = [
        'is_passed' => 'boolean',
        'checked_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function exam()
    {
        return $this->belongsTo(CoreExam::class, 'core_exam_id');
    }

    public function examSubject()
    {
        return $this->belongsTo(CoreExamSubject::class, 'core_exam_subject_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function isAbsent()
    {
        return $this->attendance_status === 'absent';
    }

    public function isMedical()
    {
        return $this->attendance_status === 'medical';
    }

    public function isPresent()
    {
        return $this->attendance_status === 'present';
    }

    public function getPercentageAttribute()
    {
        if (!$this->examSubject || $this->examSubject->max_marks <= 0) {
            return 0;
        }

        return round(
            ($this->marks_obtained / $this->examSubject->max_marks) * 100,
            2
        );
    }
}
