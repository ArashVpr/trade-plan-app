<?php

namespace Database\Factories;

use App\Models\Checklist;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Checklist>
 */
class ChecklistFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Checklist::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'zone_qualifiers' => [
                'freshness' => $this->faker->boolean,
                'strength' => $this->faker->boolean,
                'profit_margin' => $this->faker->boolean,
                'risk_reward' => $this->faker->boolean,
            ],
            'technicals' => [
                'higher_time_frame_trend' => $this->faker->randomElement(['uptrend', 'downtrend', 'sideways']),
                'higher_time_frame_level' => $this->faker->boolean,
                'curve' => $this->faker->randomElement(['extreme', 'discount', 'premium']),
            ],
            'fundamentals' => [
                'cot_commercials' => $this->faker->randomElement(['net_long', 'net_short']),
                'cot_index' => $this->faker->randomElement(['extreme_long', 'extreme_short', 'neutral']),
                'seasonal_tendency' => $this->faker->boolean,
            ],
            'score' => $this->faker->numberBetween(0, 100),
            'asset' => $this->faker->randomElement(['EURUSD', 'GBPUSD', 'AUDUSD', 'USDCAD', 'USDJPY', 'XAUUSD']),
        ];
    }
}
