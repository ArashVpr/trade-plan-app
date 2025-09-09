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
        Schema::create('trade_management_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trade_entry_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['technical', 'fundamental', 'risk', 'time', 'custom']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_predefined')->default(false);
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['pending', 'completed', 'ignored', 'triggered'])->default('pending');
            $table->datetime('due_date')->nullable();
            $table->datetime('triggered_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // For storing additional data like price levels, news events, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_management_items');
    }
};
