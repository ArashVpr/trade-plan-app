<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('zone_fresh_weight')->default(5);
            $table->integer('zone_original_weight')->default(5);
            $table->integer('zone_flip_weight')->default(5);
            $table->integer('zone_lol_weight')->default(5);
            $table->integer('zone_min_profit_margin_weight')->default(5);
            $table->integer('zone_big_brother_weight')->default(5);
            $table->integer('technical_very_exp_chp_weight')->default(12);
            $table->integer('technical_exp_chp_weight')->default(7);
            $table->integer('technical_direction_impulsive_weight')->default(12);
            $table->integer('technical_direction_correction_weight')->default(6);
            $table->integer('fundamental_valuation_weight')->default(13);
            $table->integer('fundamental_seasonal_weight')->default(6);
            $table->integer('fundamental_cot_index_weight')->default(12);
            $table->integer('fundamental_noncommercial_divergence_weight')->default(15);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropColumn([
                'zone_fresh_weight',
                'zone_original_weight',
                'zone_flip_weight',
                'zone_lol_weight',
                'zone_min_profit_margin_weight',
                'zone_big_brother_weight',
                'technical_very_exp_chp_weight',
                'technical_exp_chp_weight',
                'technical_direction_impulsive_weight',
                'technical_direction_correction_weight',
                'fundamental_valuation_weight',
                'fundamental_seasonal_weight',
                'fundamental_cot_index_weight',
                'fundamental_noncommercial_divergence_weight',
            ]);
        });
    }
};
