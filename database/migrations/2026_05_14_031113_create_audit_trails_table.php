<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable(); // The actor
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->string('action_type'); // created, updated, deleted, generated, refreshed, etc
            $table->string('model_type')->nullable(); // The model affected
            $table->unsignedBigInteger('model_id')->nullable(); // The model's ID
            $table->json('old_values')->nullable(); // State before
            $table->json('new_values')->nullable(); // State after
            $table->string('ip_address', 45)->nullable(); // IPv4 or IPv6
            $table->string('description')->nullable(); // Human readable summary
            $table->timestamps();
            
            // Indexes for fast searching
            $table->index(['model_type', 'model_id']);
            $table->index('action_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_trails');
    }
};
