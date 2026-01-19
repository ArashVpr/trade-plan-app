<?php

namespace Database\Seeders;

use App\Models\AnalysisTracker;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AnalysisTrackerSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first(); // Use the first user

        $trackers = [
            [
                'user_id' => $user->id,
                'symbol' => 'EURUSD',
                'tracked_metrics' => [
                    'zone_qualifiers' => ['Fresh', 'Original'],
                    'technicals' => [
                        'location' => 'Very Cheap',
                        'direction' => 'Impulsive'
                    ],
                    'fundamentals' => [
                        'valuation' => 'Undervalued',
                        'seasonalConfluence' => 'Bullish'
                    ]
                ],
                'last_updated_at' => now()->subDays(1),
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(1),
            ],
            [
                'user_id' => $user->id,
                'symbol' => 'GBPUSD',
                'tracked_metrics' => [
                    'zone_qualifiers' => ['Fresh', 'LOL', 'Big Brother Coverage'],
                    'technicals' => [
                        'location' => 'Cheap',
                        'direction' => 'Corrective'
                    ],
                    'fundamentals' => [
                        'valuation' => 'Neutral',
                        'seasonalConfluence' => ''
                    ]
                ],
                'last_updated_at' => now()->subDays(2),
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(2),
            ],
            [
                'user_id' => $user->id,
                'symbol' => 'USDJPY',
                'tracked_metrics' => [
                    'zone_qualifiers' => ['Original'],
                    'technicals' => [
                        'location' => 'Very Expensive',
                        'direction' => ''
                    ],
                    'fundamentals' => [
                        'valuation' => '',
                        'seasonalConfluence' => ''
                    ]
                ],
                'last_updated_at' => now()->subDays(8), // Stale tracker
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(8),
            ],
            [
                'user_id' => $user->id,
                'symbol' => 'AUDUSD',
                'tracked_metrics' => [
                    'zone_qualifiers' => ['Fresh', 'Original', 'Flip', 'Minimum 1:2 Profit Margin'],
                    'technicals' => [
                        'location' => 'Cheap',
                        'direction' => 'Impulsive'
                    ],
                    'fundamentals' => [
                        'valuation' => 'Undervalued',
                        'seasonalConfluence' => 'Bullish'
                    ]
                ],
                'last_updated_at' => now()->subHours(6),
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subHours(6),
            ],
        ];

        foreach ($trackers as $trackerData) {
            $tracker = AnalysisTracker::create($trackerData);
            $tracker->updateCompletion(); // Calculate completion percentage
        }

        $this->command->info('Created ' . count($trackers) . ' analysis tracker entries');
    }
}
