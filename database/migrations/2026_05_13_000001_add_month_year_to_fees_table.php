<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            $table->unsignedTinyInteger('month')->nullable()->after('billing_cycle');
            $table->unsignedSmallInteger('year')->nullable()->after('month');
            $table->boolean('is_locked')->default(false)->after('status');
            $table->string('payment_period', 20)->nullable()->after('billing_cycle');
        });

        // Backfill month/year from billing_cycle (format: YYYY-MM)
        DB::statement("
            UPDATE fees
            SET
                year = LEFT(billing_cycle, 4),
                month = MID(billing_cycle, 6, 2),
                payment_period = CONCAT(LEFT(billing_cycle, 4), '-', LPAD(MID(billing_cycle, 6, 2), 2, '0'))
            WHERE billing_cycle IS NOT NULL AND billing_cycle != ''
        ");
    }

    public function down(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            $table->dropColumn(['month', 'year', 'is_locked', 'payment_period']);
        });
    }
};