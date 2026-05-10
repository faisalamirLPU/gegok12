<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoreExamSubject extends Model
{
    protected $fillable = [
        'core_exam_id',
        'subject_id',
        'subject_name',
        'max_marks',
        'pass_marks',
        'exam_date',
    ];

    protected $casts = [
        'exam_date' => 'date',
    ];

    public function exam()
    {
        return $this->belongsTo(CoreExam::class, 'core_exam_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function marks()
    {
        return $this->hasMany(CoreMark::class);
    }
}
