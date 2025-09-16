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

        // Get symbols from database instead of hardcoded array
        $instruments = \App\Models\Instrument::active()->pluck('symbol')->toArray();
        $symbols = !empty($instruments) ? $instruments : ['EURUSD', 'GBPUSD', 'USDJPY', 'AUDUSD', 'USDCAD', 'NZDUSD', 'EURGBP', 'EURJPY', 'GBPJPY', 'AUDJPY'];

        return [
            'user_id' => 1, // Using static user ID for now, could use User::factory()
            'zone_qualifiers' => $selectedQualifiers,
            'technicals' => [
                'location' => $this->faker->randomElement(['Very Expensive', 'Expensive', 'EQ', 'Cheap', 'Very Cheap']),
                'direction' => $this->faker->randomElement(['Correction', 'Impulsion']),
            ],
            'fundamentals' => [
                'valuation' => $this->faker->randomElement(['Overvalued', 'Neutral', 'Undervalued']),
                'seasonalConfluence' => $this->faker->randomElement(['Bullish', 'Neutral', 'Bearish']),
                'nonCommercials' => $this->faker->randomElement(['Bullish Divergence', 'Neutral', 'Bearish Divergence']),
                'cotIndex' => $this->faker->randomElement(['Bullish', 'Neutral', 'Bearish']),
            ],
            'score' => $this->faker->numberBetween(0, 100),
            'symbol' => $this->faker->randomElement($symbols),
            'created_at' => $this->faker->dateTimeBetween('-4 weeks', 'now'),
        ];
    }

    /**
     * Create checklist with no trade entry (analysis only)
     */
    public function analysisOnly()
    {
        return $this->state(function (array $attributes) {
            return [
                // Just the checklist, no trade entry will be created
            ];
        });
    }

    /**
     * Create checklist with pending order
     */
    public function withPendingOrder()
    {
        return $this->state(function (array $attributes) {
            return [];
        })->afterCreating(function ($checklist) {
            \App\Models\TradeEntry::factory()->pending()->create([
                'checklist_id' => $checklist->id,
            ]);
        });
    }

    /**
     * Create checklist with active position
     */
    public function withActivePosition()
    {
        return $this->state(function (array $attributes) {
            return [];
        })->afterCreating(function ($checklist) {
            \App\Models\TradeEntry::factory()->active()->create([
                'checklist_id' => $checklist->id,
            ]);
        });
    }

    /**
     * Create checklist with completed trade
     */
    public function withCompletedTrade()
    {
        return $this->state(function (array $attributes) {
            return [];
        })->afterCreating(function ($checklist) {
            \App\Models\TradeEntry::factory()->completed()->create([
                'checklist_id' => $checklist->id,
            ]);
        });
    }

    /**
     * Create checklist with cancelled trade
     */
    public function withCancelledTrade()
    {
        return $this->state(function (array $attributes) {
            return [];
        })->afterCreating(function ($checklist) {
            \App\Models\TradeEntry::factory()->cancelled()->create([
                'checklist_id' => $checklist->id,
            ]);
        });
    }
}
