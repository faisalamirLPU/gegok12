<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_special_fees', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('school_id');

            $table->unsignedBigInteger('academic_year_id');

            $table->unsignedBigInteger('user_id');

            $table->unsignedBigInteger('fee_category_id');

            $table->decimal('amount', 10, 2)->default(0);

            $table->date('due_date')->nullable();

            $table->text('remarks')->nullable();

            $table->tinyInteger('status')->default(1);

            $table->timestamps();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_special_fees');
    }
};
