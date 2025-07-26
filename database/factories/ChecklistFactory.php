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
        $zoneQualifiers = ['Fresh', 'Original', 'Flip', 'LOL', 'Minimum 1:2 Profit Margin', 'Big Brother Coverage'];
        $selectedQualifiers = $this->faker->randomElements($zoneQualifiers, $this->faker->numberBetween(1, 4));

        $symbols = ['EURUSD', 'GBPUSD', 'USDJPY', 'AUDUSD', 'USDCAD', 'NZDUSD', 'EURGBP', 'EURJPY', 'GBPJPY', 'AUDJPY'];
        $assets = ['EUR/USD', 'GBP/USD', 'USD/JPY', 'AUD/USD', 'USD/CAD', 'NZD/USD', 'EUR/GBP', 'EUR/JPY', 'GBP/JPY', 'AUD/JPY'];

        return [
            'user_id' => 1, // Using static user ID for now, could use User::factory()
            'zone_qualifiers' => $selectedQualifiers,
            'technicals' => [
                'location' => $this->faker->randomElement(['Very Expensive', 'Expensive', 'EQ', 'Cheap', 'Very Cheap']),
                'direction' => $this->faker->randomElement(['Correction', 'Impulsion']),
            ],
            'fundamentals' => [
                'valuation' => $this->faker->randomElement(['Overvalued', 'Neutral', 'Undervalued']),
                'seasonalConfluence' => $this->faker->randomElement(['Yes', 'No']),
                'nonCommercials' => $this->faker->randomElement(['Divergence', 'No-Divergence']),
                'cotIndex' => $this->faker->randomElement(['Bullish', 'Neutral', 'Bearish']),
            ],
            'score' => $this->faker->numberBetween(0, 100),
            'asset' => $this->faker->randomElement($assets),
            'symbol' => $this->faker->randomElement($symbols),
            'bias' => $this->faker->randomElement(['Long', 'Short']),
            'notes' => $this->faker->optional(0.7)->sentence(),
            'status' => $this->faker->randomElement(['planned', 'executed', 'cancelled']),
            'created_at' => $this->faker->dateTimeBetween('-4 weeks', 'now'),
        ];
    }
}
