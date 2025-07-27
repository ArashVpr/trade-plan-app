<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trade_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('checklist_id')->constrained()->cascadeOnDelete();
            $table->date('entry_date');
            $table->enum('position_type', ['Long', 'Short'])->nullable();
            $table->decimal('entry_price', 12, 4);
            $table->decimal('stop_price', 12, 4);
            $table->decimal('target_price', 12, 4);
            $table->enum('trade_status', [
                'pending',          // Order placed, awaiting execution
                'active',           // Position opened, trade running
                'win',    // Trade closed with profit
                'loss',   // Trade closed with loss
                'breakeven', // Trade closed at breakeven
                'cancelled'         // Order cancelled before execution
            ])->default('pending');
            $table->decimal('rrr', 4, 2)->nullable(); // Risk-Reward Ratio
            $table->string('screenshot_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trade_entries');
    }
};
