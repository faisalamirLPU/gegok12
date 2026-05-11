<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_structures', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('academic_year_id');

            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('section_id')->nullable();

            $table->string('title');
            $table->text('description')->nullable();

            $table->enum('installment_type', [
                'monthly',
                'quarterly',
                'half_yearly',
                'yearly',
                'custom'
            ])->default('monthly');

            $table->enum('due_type', [
                'fixed_date',
                'monthly_cycle',
                'custom'
            ])->default('monthly_cycle');

            $table->boolean('status')->default(true);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'academic_year_id']);
            $table->index(['class_id', 'section_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_structures');
    }
};
