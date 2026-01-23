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
        Schema::table('trade_entries', function (Blueprint $table) {
            $table->date('entry_date')->nullable()->change();
            $table->decimal('entry_price', 12, 4)->nullable()->change();
            $table->decimal('stop_price', 12, 4)->nullable()->change();
            $table->decimal('target_price', 12, 4)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trade_entries', function (Blueprint $table) {
            // Ideally we would revert these, but reverting to NOT NULL fails if null data exists.
            // Leaving empty for safety in this specific context unless strict rollback is required.
            $table->date('entry_date')->nullable(false)->change();
            $table->decimal('entry_price', 12, 4)->nullable(false)->change();
            $table->decimal('stop_price', 12, 4)->nullable(false)->change();
            $table->decimal('target_price', 12, 4)->nullable(false)->change();
        });
    }
};
