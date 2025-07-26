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
        Schema::create('checklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Foreign key to users table
            $table->json('zone_qualifiers')->nullable(); // Array of selected zone qualifiers
            $table->json('technicals')->nullable(); // Location and Direction
            $table->json('fundamentals')->nullable(); // Valuation, Seasonal Confluence, Non-Commercials, CoT Index
            $table->integer('score')->default(0); // Calculated evaluation score
            $table->enum('status', ['planned', 'executed', 'cancelled'])->default('planned');
            $table->string('asset')->nullable(); // Optional: Asset being evaluated (e.g., stock, forex pair)
            $table->string('symbol')->nullable();
            $table->enum('bias', ['Long', 'Short'])->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Foreign key constraint to ensure checklist belongs to a user
        });

        Schema::create('checklist_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->json('scoring_weights')->nullable(); // Custom weights for scoring (e.g., Zone Qualifiers: 30%)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklists');
        Schema::dropIfExists('checklist_settings');
    }
};
