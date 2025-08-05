<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TradeManagementItem>
 */
class TradeManagementItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['technical', 'fundamental', 'risk', 'time', 'custom'];
        $priorities = ['low', 'medium', 'high', 'critical'];
        $statuses = ['pending', 'completed', 'ignored', 'triggered'];

        $type = $this->faker->randomElement($types);

        // Type-specific titles and descriptions
        $itemData = $this->getItemDataByType($type);

        return [
            'type' => $type,
            'title' => $this->faker->randomElement($itemData['titles']),
            'description' => $this->faker->randomElement($itemData['descriptions']),
            'is_predefined' => $this->faker->boolean(70), // 70% chance of being predefined
            'priority' => $this->faker->randomElement($priorities),
            'status' => $this->faker->randomElement($statuses),
            'due_date' => $this->faker->optional(0.6)->dateTimeBetween('now', '+30 days'),
            'triggered_at' => function (array $attributes) {
                return in_array($attributes['status'], ['completed', 'triggered'])
                    ? $this->faker->dateTimeBetween('-7 days', 'now')
                    : null;
            },
            'notes' => $this->faker->optional(0.4)->sentence(),
            'metadata' => function (array $attributes) {
                // Add metadata based on type
                switch ($attributes['type']) {
                    case 'technical':
                        return [
                            'price_level' => $this->faker->randomFloat(2, 1.0, 100.0),
                            'zone_type' => $this->faker->randomElement(['support', 'resistance', 'supply', 'demand'])
                        ];
                    case 'fundamental':
                        return [
                            'event_source' => $this->faker->randomElement(['economic_calendar', 'news', 'earnings']),
                            'impact' => $this->faker->randomElement(['low', 'medium', 'high'])
                        ];
                    case 'risk':
                        return [
                            'risk_percentage' => $this->faker->randomFloat(1, 1.0, 5.0),
                            'position_size' => $this->faker->randomFloat(2, 0.1, 2.0)
                        ];
                    default:
                        return null;
                }
            },
        ];
    }

    /**
     * Get item data based on type
     */
    private function getItemDataByType($type)
    {
        $data = [
            'technical' => [
                'titles' => [
                    'Approaching Supply Zone',
                    'Approaching Demand Zone',
                    'Breaking Key Support',
                    'Breaking Key Resistance',
                    'Trend Line Break',
                    'Moving Average Cross',
                    'RSI Divergence'
                ],
                'descriptions' => [
                    'Price is approaching a significant supply zone',
                    'Price is approaching a significant demand zone',
                    'Price has broken below key support level',
                    'Price has broken above key resistance level',
                    'Price has broken the trend line',
                    'Price crossed above/below moving average',
                    'RSI showing bullish/bearish divergence'
                ]
            ],
            'fundamental' => [
                'titles' => [
                    'News Event Within 24 Hours',
                    'Economic Data Release',
                    'Earnings Announcement',
                    'Central Bank Meeting',
                    'FOMC Decision',
                    'NFP Release',
                    'CPI Data'
                ],
                'descriptions' => [
                    'Important news event scheduled within next 24 hours',
                    'Major economic data release affecting this instrument',
                    'Company earnings announcement scheduled',
                    'Central bank meeting/decision scheduled',
                    'Federal Reserve policy decision pending',
                    'Non-farm payrolls data release',
                    'Consumer Price Index data release'
                ]
            ],
            'risk' => [
                'titles' => [
                    'Move Stop to Breakeven',
                    'Scale Out 50%',
                    'Trail Stop Loss',
                    'Max Risk Exceeded',
                    'Position Size Review',
                    'Correlation Risk Check'
                ],
                'descriptions' => [
                    'Trade has reached 1R profit, consider moving stop to breakeven',
                    'Trade has reached first target, consider scaling out 50%',
                    'Start trailing stop loss as trade moves in favor',
                    'Trade has exceeded maximum risk threshold',
                    'Review position size relative to account balance',
                    'Check for correlation with other open positions'
                ]
            ],
            'time' => [
                'titles' => [
                    'Weekly Trade Review',
                    'End of Month Review',
                    'Trade Timeout',
                    'Session Close Review',
                    'Weekend Analysis'
                ],
                'descriptions' => [
                    'Review trade performance and adjust strategy if needed',
                    'Monthly assessment of trade performance',
                    'Trade has been open for predefined maximum time',
                    'Review trade before session close',
                    'Weekend market analysis and preparation'
                ]
            ],
            'custom' => [
                'titles' => [
                    'Custom Reminder',
                    'Personal Note',
                    'Strategy Review',
                    'Market Observation',
                    'Trade Idea'
                ],
                'descriptions' => [
                    'Custom reminder set by trader',
                    'Personal note about the trade',
                    'Review trading strategy effectiveness',
                    'Important market observation to consider',
                    'Trade idea for future consideration'
                ]
            ]
        ];

        return $data[$type] ?? $data['custom'];
    }

    /**
     * Create a high priority item
     */
    public function highPriority()
    {
        return $this->state(fn(array $attributes) => [
            'priority' => 'high',
        ]);
    }

    /**
     * Create a critical priority item
     */
    public function critical()
    {
        return $this->state(fn(array $attributes) => [
            'priority' => 'critical',
            'status' => 'pending',
        ]);
    }

    /**
     * Create a completed item
     */
    public function completed()
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'completed',
            'triggered_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'notes' => 'Completed by trader',
        ]);
    }

    /**
     * Create a pending item
     */
    public function pending()
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'pending',
            'triggered_at' => null,
        ]);
    }
}
