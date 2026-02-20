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
        Schema::create('instruments', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 20)->unique(); // e.g., 'EURUSD', 'AAPL'
            $table->string('name'); // e.g., 'Euro vs US Dollar', 'Apple Inc.'
            $table->enum('type', ['forex', 'crypto', 'commodity', 'index', 'stock']); // Instrument type
            $table->enum('category', ['major', 'minor', 'crypto', 'agricultural', 'metal', 'energy', 'top100'])->nullable(); // Sub-category
            $table->string('exchange')->nullable(); // e.g., 'NYSE', 'NASDAQ' for stocks
            $table->boolean('is_active')->default(true); // To enable/disable instruments
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instruments');
    }
};
