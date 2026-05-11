<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fine_rules', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('academic_year_id');

            $table->string('name');

            $table->enum('fine_type', [
                'fixed',
                'percentage',
                'daily'
            ])->default('daily');

            $table->decimal('amount', 12, 2)->default(0);

            $table->integer('grace_days')->default(0);

            $table->decimal('max_limit', 12, 2)->nullable();

            $table->boolean('status')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'academic_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fine_rules');
    }
};
