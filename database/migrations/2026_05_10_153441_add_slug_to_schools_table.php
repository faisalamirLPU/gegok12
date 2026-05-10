<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run migrations.
     */
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {

            if (!Schema::hasColumn('schools', 'slug')) {

                $table->string('slug')
                    ->nullable()
                    ->unique()
                    ->after('name');
            }

        });
    }

    /**
     * Reverse migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {

            if (Schema::hasColumn('schools', 'slug')) {

                $table->dropColumn('slug');
            }

        });
    }
};
