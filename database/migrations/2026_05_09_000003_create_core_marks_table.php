<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('core_marks', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Exam Relations
            |--------------------------------------------------------------------------
            */

            $table->foreignId('core_exam_id')
                ->constrained('core_exams')
                ->cascadeOnDelete();

            $table->foreignId('core_exam_subject_id')
                ->constrained('core_exam_subjects')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Student
            |--------------------------------------------------------------------------
            */

            $table->integer('student_id');

            /*
            |--------------------------------------------------------------------------
            | Marks
            |--------------------------------------------------------------------------
            */

            $table->decimal(
                'marks_obtained',
                8,
                2
            )->nullable();

            /*
            |--------------------------------------------------------------------------
            | Attendance
            |--------------------------------------------------------------------------
            */

            $table->enum(
                'attendance_status',
                [
                    'present',
                    'absent',
                    'medical'
                ]
            )->default('present');

            /*
            |--------------------------------------------------------------------------
            | Grade
            |--------------------------------------------------------------------------
            */

            $table->string('grade')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Pass / Fail
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_passed')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Remarks
            |--------------------------------------------------------------------------
            */

            $table->text('remarks')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | ERP Audit
            |--------------------------------------------------------------------------
            */

            $table->integer('checked_by')
                ->nullable();

            $table->timestamp('checked_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Unique Constraint
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'core_exam_subject_id',
                'student_id'
            ]);

            /*
            |--------------------------------------------------------------------------
            | Performance Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('student_id');

            $table->index('core_exam_id');

            $table->index('core_exam_subject_id');

            $table->index('attendance_status');

            $table->index('is_passed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_marks');
    }
};
