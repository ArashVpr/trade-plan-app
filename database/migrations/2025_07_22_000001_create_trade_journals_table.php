<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trade_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('instrument');
            $table->date('entry_date');
            $table->decimal('entry_price', 12, 4);
            $table->decimal('stop_price', 12, 4);
            $table->decimal('target_price', 12, 4);
            $table->text('notes')->nullable();
            $table->tinyInteger('setup_grade')->nullable();
            $table->string('screenshot_path')->nullable();
            $table->enum('outcome', ['win', 'loss', 'breakeven'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trade_journals');
    }
};
