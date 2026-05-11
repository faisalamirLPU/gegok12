<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_categories', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('academic_year_id');

            $table->string('name');
            $table->string('code')->unique();

            $table->enum('frequency', [
                'one_time',
                'monthly',
                'quarterly',
                'half_yearly',
                'yearly'
            ])->default('monthly');

            $table->boolean('is_refundable')->default(false);
            $table->boolean('is_optional')->default(false);

            $table->text('description')->nullable();

            $table->boolean('status')->default(true);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'academic_year_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_categories');
    }
};
