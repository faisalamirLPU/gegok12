<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_structure_items', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('fee_structure_id');
            $table->unsignedBigInteger('fee_category_id');

            $table->unsignedBigInteger('fine_rule_id')->nullable();

            $table->decimal('amount', 12, 2)->default(0);

            $table->date('due_date')->nullable();

            $table->boolean('is_optional')->default(false);

            $table->integer('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('fee_structure_id');
            $table->index('fee_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_structure_items');
    }
};
