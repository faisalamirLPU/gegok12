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
        Schema::create('student_fee_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id');
            $table->foreignId('academic_year_id');
            $table->foreignId('user_id');
            $table->foreignId('standard_link_id')->nullable();
            $table->foreignId('fee_id')->nullable()->comment('Links to fee_structures table');
            
            $table->decimal('assigned_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);
            
            $table->date('due_date')->nullable();
            $table->date('assigned_on')->nullable();
            
            $table->tinyInteger('status')->default(0)->comment('0: Pending, 1: Active, 2: Completed');
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_fee_assignments');
    }
};
