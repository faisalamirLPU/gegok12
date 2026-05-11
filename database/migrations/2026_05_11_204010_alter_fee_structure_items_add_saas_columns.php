<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_structure_items', function (Blueprint $table) {

            $table->unsignedBigInteger('school_id')
                ->after('id');

            $table->unsignedBigInteger('academic_year_id')
                ->after('school_id');

            $table->unsignedBigInteger('created_by')
                ->nullable()
                ->after('sort_order');

            $table->unsignedBigInteger('updated_by')
                ->nullable()
                ->after('created_by');

            $table->index([
                'school_id',
                'academic_year_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('fee_structure_items', function (Blueprint $table) {

            $table->dropIndex([
                'school_id',
                'academic_year_id'
            ]);

            $table->dropColumn([

                'school_id',
                'academic_year_id',

                'created_by',
                'updated_by'
            ]);
        });
    }
};
