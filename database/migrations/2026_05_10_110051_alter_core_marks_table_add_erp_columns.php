<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('core_marks', function (Blueprint $table) {

            if (!Schema::hasColumn('core_marks', 'checked_by')) {

                $table->integer('checked_by')
                    ->nullable()
                    ->after('remarks');
            }

            if (!Schema::hasColumn('core_marks', 'checked_at')) {

                $table->timestamp('checked_at')
                    ->nullable()
                    ->after('checked_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('core_marks', function (Blueprint $table) {

            if (Schema::hasColumn('core_marks', 'checked_by')) {

                $table->dropColumn('checked_by');
            }

            if (Schema::hasColumn('core_marks', 'checked_at')) {

                $table->dropColumn('checked_at');
            }
        });
    }
};
