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
        Schema::create('checklist_wizard', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->json('zone_qualifiers')->nullable(); // Array of selected zone qualifiers
            $table->json('technicals')->nullable(); // Location and Direction
            $table->json('fundamentals')->nullable(); // Valuation, Seasonal Confluence, Non-Commercials, CoT Index
            $table->integer('score')->default(0); // Calculated evaluation score
            $table->string('asset')->nullable(); // Optional: Asset being evaluated (e.g., stock, forex pair)
            $table->text('notes')->nullable(); // Optional: User notes for the checklist
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('checklist_wizard');
        Schema::dropIfExists('checklist_settings');
    }
};
