<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_structures', function (Blueprint $table) {

            $table->boolean('is_global')
                ->default(false)
                ->after('class_id');

            $table->string('structure_scope')
                ->default('class')
                ->after('is_global');

            $table->unsignedBigInteger('class_id')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('fee_structures', function (Blueprint $table) {

            $table->dropColumn([
                'is_global',
                'structure_scope'
            ]);
        });
    }
};