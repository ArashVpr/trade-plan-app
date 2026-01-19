<?php

namespace Database\Factories;

use App\Models\AnalysisTracker;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AnalysisTracker>
 */
class AnalysisTrackerFactory extends Factory
{
    protected $model = AnalysisTracker::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $symbols = ['EURUSD', 'GBPUSD', 'USDJPY', 'AUDUSD', 'USDCAD', 'NZDUSD', 'EURGBP', 'EURJPY', 'GBPJPY', 'AUDJPY'];
        
        $zoneQualifiers = ['Fresh', 'Original', 'Flip', 'LOL', 'Minimum 1:2 Profit Margin', 'Big Brother Coverage'];
        $locations = ['Very Expensive', 'Expensive', 'Fair Value', 'Cheap', 'Very Cheap'];
        $directions = ['Impulsive', 'Corrective'];
        $valuations = ['Overvalued', 'Neutral', 'Undervalued'];
        $seasonals = ['Bullish', 'Neutral', 'Bearish'];

        // Randomly select some zone qualifiers
        $selectedQualifiers = $this->faker->randomElements($zoneQualifiers, $this->faker->numberBetween(0, 3));
        
        // Randomly determine if technicals are set
        $technicalsSet = $this->faker->boolean(70);
        $fundamentalsSet = $this->faker->boolean(60);

        $trackedMetrics = [
            'zone_qualifiers' => $selectedQualifiers,
            'technicals' => [
                'location' => $technicalsSet ? $this->faker->randomElement($locations) : '',
                'direction' => $technicalsSet ? $this->faker->randomElement($directions) : ''
            ],
            'fundamentals' => [
                'valuation' => $fundamentalsSet ? $this->faker->randomElement($valuations) : '',
                'seasonalConfluence' => $fundamentalsSet ? $this->faker->randomElement($seasonals) : ''
            ]
        ];

        return [
            'user_id' => User::factory(),
            'symbol' => $this->faker->randomElement($symbols),
            'tracked_metrics' => $trackedMetrics,
            'completion_percentage' => 0, // Will be calculated by the model
            'last_updated_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * Create a tracker that's ready for checklist (high completion)
     */
    public function readyForChecklist()
    {
        return $this->state(function (array $attributes) {
            return [
                'tracked_metrics' => [
                    'zone_qualifiers' => ['Fresh', 'Original', 'LOL'],
                    'technicals' => [
                        'location' => 'Very Cheap',
                        'direction' => 'Impulsive'
                    ],
                    'fundamentals' => [
                        'valuation' => 'Undervalued',
                        'seasonalConfluence' => 'Bullish'
                    ]
                ],
                'last_updated_at' => now()->subHours($this->faker->numberBetween(1, 48)),
            ];
        });
    }

    /**
     * Create a stale tracker (not updated recently)
     */
    public function stale()
    {
        return $this->state(function (array $attributes) {
            return [
                'last_updated_at' => $this->faker->dateTimeBetween('-30 days', '-8 days'),
            ];
        });
    }
}
