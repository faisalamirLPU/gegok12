<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core_fee_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('core_fee_head_id')->constrained('core_fee_heads')->cascadeOnDelete();
            $table->unsignedInteger('student_id');
            $table->unsignedTinyInteger('fee_month')->nullable();
            $table->unsignedSmallInteger('fee_year')->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('due_date')->nullable();
            $table->enum('status', ['unpaid', 'paid', 'waived'])->default('unpaid');
            $table->dateTime('paid_at')->nullable();
            $table->string('payment_mode')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['core_fee_head_id', 'student_id', 'fee_month', 'fee_year'], 'core_fee_invoice_period_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core_fee_invoices');
    }
};
