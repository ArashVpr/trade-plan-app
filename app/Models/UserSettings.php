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
}
