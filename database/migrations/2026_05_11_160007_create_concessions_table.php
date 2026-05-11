<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('concessions', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('academic_year_id');

            $table->unsignedBigInteger('student_id');

            $table->enum('type', [
                'fixed',
                'percentage',
                'scholarship',
                'sibling',
                'staff'
            ])->default('fixed');

            $table->decimal('amount', 12, 2)->default(0);

            $table->text('reason')->nullable();

            $table->unsignedBigInteger('approved_by')->nullable();

            $table->enum('status', [
                'pending',
                'approved',
                'rejected'
            ])->default('pending');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'academic_year_id']);
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('concessions');
    }
};
