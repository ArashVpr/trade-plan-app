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
        Schema::table('checklists', function (Blueprint $table) {
            // Add columns that the dashboard expects
            $table->string('symbol')->nullable()->after('asset'); // Trading symbol (e.g., AAPL, EURUSD)
            $table->enum('bias', ['Long', 'Short'])->nullable()->after('symbol'); // Trading bias
            $table->decimal('overall_score', 3, 1)->nullable()->after('score'); // Overall score out of 10
            $table->text('notes')->nullable()->after('overall_score'); // Additional notes
            $table->enum('status', ['planned', 'executed', 'cancelled'])->default('planned')->after('notes'); // Trade status
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checklists', function (Blueprint $table) {
            $table->dropColumn(['symbol', 'bias', 'overall_score', 'notes', 'status']);
        });
    }
};
