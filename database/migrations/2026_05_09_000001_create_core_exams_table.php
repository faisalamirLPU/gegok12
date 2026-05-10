<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->unsignedInteger('academic_year_id')->nullable();
            $table->unsignedInteger('standard_link_id')->nullable();
            $table->string('name');
            $table->date('exam_date')->nullable();
            $table->decimal('max_marks', 8, 2)->default(100);
            $table->decimal('pass_marks', 8, 2)->default(35);
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();

            $table->foreign('academic_year_id')->references('id')->on('academic_years')->nullOnDelete();
            $table->foreign('standard_link_id')->references('id')->on('standards_link')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core_exams');
    }
};
