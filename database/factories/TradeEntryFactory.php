<?php

namespace Database\Factories;

use App\Models\TradeEntry;
use App\Models\User;
use App\Models\Checklist;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TradeEntry>
 */
class TradeEntryFactory extends Factory
{
    protected $model = TradeEntry::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $entryPrice = $this->faker->randomFloat(4, 1.0000, 2.0000);
        $isLong = $this->faker->boolean();

        // Calculate realistic stop and target prices
        $stopDistance = $this->faker->randomFloat(4, 0.0020, 0.0100); // 20-100 pips
        $targetDistance = $this->faker->randomFloat(4, 0.0040, 0.0200); // 40-200 pips

        $stopPrice = $isLong ? $entryPrice - $stopDistance : $entryPrice + $stopDistance;
        $targetPrice = $isLong ? $entryPrice + $targetDistance : $entryPrice - $targetDistance;

        return [
            'user_id' => 1,
            'checklist_id' => Checklist::factory(),
            'instrument' => $this->faker->randomElement(['EUR/USD', 'GBP/USD', 'USD/JPY', 'AUD/USD', 'USD/CAD']),
            'entry_date' => $this->faker->dateTimeBetween('-2 weeks', 'now'),
            'position_type' => $isLong ? 'Long' : 'Short',
            'entry_price' => $entryPrice,
            'stop_price' => $stopPrice,
            'target_price' => $targetPrice,
            'rrr' => round($targetDistance / $stopDistance, 2),
            'trade_status' => 'pending',
            'outcome' => null,
            'notes' => $this->faker->optional(0.6)->sentence(),
        ];
    }

    /**
     * Order is pending execution
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'trade_status' => 'pending',
                'outcome' => null,
                'notes' => $this->faker->optional(0.7)->sentence(),
            ];
        });
    }

    /**
     * Position is active/running
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'trade_status' => 'active',
                'outcome' => null,
                'entry_date' => $this->faker->dateTimeBetween('-1 week', 'now'),
                'notes' => $this->faker->optional(0.5)->sentence(),
            ];
        });
    }

    /**
     * Trade is completed with outcome
     */
    public function completed()
    {
        return $this->state(function (array $attributes) {
            $outcome = $this->faker->randomElement(['win', 'loss', 'breakeven']);

            // Calculate realistic R:R based on outcome
            $rrr = $this->calculateRRByOutcome($outcome);

            return [
                'trade_status' => 'completed',
                'outcome' => $outcome,
                'rrr' => $rrr,
                'entry_date' => $this->faker->dateTimeBetween('-3 weeks', '-1 day'),
                'notes' => $this->getOutcomeNote($outcome),
            ];
        });
    }

    /**
     * Trade was cancelled before execution
     */
    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'trade_status' => 'cancelled',
                'outcome' => null,
                'notes' => $this->faker->randomElement([
                    'Market conditions changed, cancelled order.',
                    'Better opportunity emerged, cancelled this trade.',
                    'Risk management - cancelled due to news.',
                    'Setup invalidated, order cancelled.',
                ]),
            ];
        });
    }

    /**
     * Calculate realistic R:R ratio based on trade outcome
     */
    private function calculateRRByOutcome($outcome)
    {
        switch ($outcome) {
            case 'win':
                // Winners can have various R:R ratios (0.5 to 3.0)
                return $this->faker->randomFloat(2, 0.5, 3.0);

            case 'loss':
                // Losses should be -1 (full stop loss) or partial loss
                return $this->faker->randomElement([
                    -1.0,  // Full stop loss hit (most common)
                    -0.8,  // Partial loss (rare - maybe scaled out)
                    -0.9,  // Almost full stop loss
                ]);

            case 'breakeven':
                // Breakeven should be around 0
                return $this->faker->randomFloat(2, -0.1, 0.1);

            default:
                return 1.0;
        }
    }

    /**
     * Generate realistic notes based on outcome
     */
    private function getOutcomeNote($outcome)
    {
        $notes = [
            'win' => [
                'Trade hit target perfectly. Good entry timing.',
                'Market moved as expected, solid profit.',
                'Excellent risk-reward execution.',
                'Target reached, managed position well.',
            ],
            'loss' => [
                'Stop loss hit, but risk was managed.',
                'Market moved against us, cut losses.',
                'Setup failed, stopped out quickly.',
                'Unexpected news event triggered stop.',
            ],
            'breakeven' => [
                'Closed at breakeven due to uncertainty.',
                'Market was choppy, took breakeven exit.',
                'Protected capital, no loss or gain.',
                'Risk management - closed flat.',
            ]
        ];

        return $this->faker->optional(0.8)->randomElement($notes[$outcome]);
    }
}
