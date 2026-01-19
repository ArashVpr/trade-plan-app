<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analysis_tracker', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('symbol');
            $table->json('tracked_metrics'); // Partial checklist data
            $table->integer('completion_percentage')->default(0);
            $table->timestamp('last_updated_at');
            $table->timestamps();
            
            $table->unique(['user_id', 'symbol']); // One tracker per symbol per user
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analysis_tracker');
    }
};
