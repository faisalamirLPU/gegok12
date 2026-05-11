<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fees', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id');

            $table->foreignId('academic_year_id');

            $table->foreignId('student_fee_assignment_id');

            $table->foreignId('user_id');

            $table->string('invoice_no')->unique();

            $table->decimal('total_amount', 12, 2);

            $table->decimal('paid_amount', 12, 2)->default(0);

            $table->decimal('balance', 12, 2);

            $table->date('due_date');

            $table->date('generated_on');

            $table->tinyInteger('status')->default(0);
            /*
            0 = unpaid
            1 = partial
            2 = paid
            */

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
