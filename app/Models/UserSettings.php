<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
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
    ];
    /**
     * Default values for each weight attribute.
     * These are applied when a new model instance is created or attribute is missing.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'zone_fresh_weight' => 4,
        'zone_original_weight' => 4,
        'zone_flip_weight' => 4,
        'zone_lol_weight' => 4,
        'zone_min_profit_margin_weight' => 4,
        'zone_big_brother_weight' => 4,
        'technical_very_exp_chp_weight' => 12,
        'technical_exp_chp_weight' => 6,
        'technical_direction_impulsive_weight' => 12,
        'technical_direction_correction_weight' => 6,
        'fundamental_valuation_weight' => 10,
        'fundamental_seasonal_weight' => 6,
        'fundamental_cot_index_weight' => 12,
        'fundamental_noncommercial_divergence_weight' => 12,
    ];
}
