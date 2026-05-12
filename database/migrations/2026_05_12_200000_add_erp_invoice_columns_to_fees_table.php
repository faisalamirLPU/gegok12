<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            if (!Schema::hasColumn('fees', 'student_academic_id')) {
                $table->unsignedBigInteger('student_academic_id')
                    ->nullable()
                    ->after('student_fee_assignment_id')
                    ->index();
            }

            if (!Schema::hasColumn('fees', 'billing_cycle')) {
                $table->string('billing_cycle', 20)
                    ->nullable()
                    ->after('invoice_no')
                    ->index();
            }
        });

        Schema::table('fees', function (Blueprint $table) {
            $table->index(
                ['school_id', 'academic_year_id', 'user_id', 'billing_cycle'],
                'fees_erp_invoice_cycle_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            $table->dropIndex('fees_erp_invoice_cycle_index');
        });

        Schema::table('fees', function (Blueprint $table) {
            if (Schema::hasColumn('fees', 'billing_cycle')) {
                $table->dropColumn('billing_cycle');
            }

            if (Schema::hasColumn('fees', 'student_academic_id')) {
                $table->dropColumn('student_academic_id');
            }
        });
    }
};
