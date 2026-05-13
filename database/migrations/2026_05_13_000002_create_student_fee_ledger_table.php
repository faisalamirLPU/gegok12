<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_fee_ledger', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('academic_year_id');
            $table->unsignedBigInteger('user_id');
            $table->string('transaction_type', 30); // credit, debit, carry_forward
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('reference_type', 50)->nullable(); // fee, payment, adjustment
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('description', 255)->nullable();
            $table->string('payment_period', 20)->nullable(); // YYYY-MM
            $table->decimal('balance_after', 12, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'user_id', 'payment_period']);
            $table->index(['user_id', 'transaction_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_fee_ledger');
    }
};