<?php

namespace Database\Factories;

use App\Models\ChecklistWeights;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChecklistWeights>
 */
class ChecklistWeightsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ChecklistWeights::class;

    /**
     * Define the model's default state.
     * Uses the default weights from the model attributes.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            // All weight fields will use default values from the model's $attributes
            // But we can explicitly set them for clarity:
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

    /**
     * Create checklist weights with custom zone weights.
     */
    public function customZoneWeights(array $weights = [])
    {
        return $this->state(function (array $attributes) use ($weights) {
            return array_merge([
                'zone_fresh_weight' => $weights['fresh'] ?? 4,
                'zone_original_weight' => $weights['original'] ?? 4,
                'zone_flip_weight' => $weights['flip'] ?? 4,
                'zone_lol_weight' => $weights['lol'] ?? 4,
                'zone_min_profit_margin_weight' => $weights['min_profit_margin'] ?? 4,
                'zone_big_brother_weight' => $weights['big_brother'] ?? 4,
            ], $attributes);
        });
    }

    /**
     * Create checklist weights with custom technical weights.
     */
    public function customTechnicalWeights(array $weights = [])
    {
        return $this->state(function (array $attributes) use ($weights) {
            return array_merge([
                'technical_very_exp_chp_weight' => $weights['very_exp_chp'] ?? 12,
                'technical_exp_chp_weight' => $weights['exp_chp'] ?? 6,
                'technical_direction_impulsive_weight' => $weights['direction_impulsive'] ?? 12,
                'technical_direction_correction_weight' => $weights['direction_correction'] ?? 6,
            ], $attributes);
        });
    }

    /**
     * Create checklist weights with custom fundamental weights.
     */
    public function customFundamentalWeights(array $weights = [])
    {
        return $this->state(function (array $attributes) use ($weights) {
            return array_merge([
                'fundamental_valuation_weight' => $weights['valuation'] ?? 10,
                'fundamental_seasonal_weight' => $weights['seasonal'] ?? 6,
                'fundamental_cot_index_weight' => $weights['cot_index'] ?? 12,
                'fundamental_noncommercial_divergence_weight' => $weights['noncommercial_divergence'] ?? 12,
            ], $attributes);
        });
    }
}
