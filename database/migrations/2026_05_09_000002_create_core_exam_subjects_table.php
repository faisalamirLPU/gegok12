<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core_exam_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('core_exam_id')->constrained('core_exams')->cascadeOnDelete();
            $table->unsignedInteger('subject_id')->nullable();
            $table->string('subject_name');
            $table->decimal('max_marks', 8, 2)->default(100);
            $table->decimal('pass_marks', 8, 2)->default(35);
            $table->date('exam_date')->nullable();
            $table->timestamps();

            $table->foreign('subject_id')->references('id')->on('subjects')->nullOnDelete();
            $table->unique(['core_exam_id', 'subject_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core_exam_subjects');
    }
};
