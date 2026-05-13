<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_optional_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('fee_category_id');
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('academic_year_id');
            $table->boolean('is_selected')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'fee_category_id', 'academic_year_id'], 'student_opt_fees_unique');
            $table->index(['user_id', 'academic_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_optional_fees');
    }
};