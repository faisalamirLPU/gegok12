<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoreExam extends Model
{
    protected $fillable = [
        'school_id',
        'academic_year_id',
        'standard_link_id',
        'name',
        'exam_date',
        'max_marks',
        'pass_marks',
        'status',
    ];

    protected $casts = [
        'exam_date' => 'date',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function standardLink()
    {
        return $this->belongsTo(StandardLink::class, 'standard_link_id');
    }

    public function subjects()
    {
        return $this->hasMany(CoreExamSubject::class);
    }

    public function marks()
    {
        return $this->hasMany(CoreMark::class);
    }
}
