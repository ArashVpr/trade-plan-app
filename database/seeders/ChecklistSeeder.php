<?php

namespace Database\Seeders;

use App\Models\Checklist;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create realistic mix of different trade scenarios

        // 1. Analysis Only (35% - 17 checklists)
        Checklist::factory()->count(17)->analysisOnly()->create();

        // 2. Pending Orders (15% - 8 checklists)
        Checklist::factory()->count(8)->withPendingOrder()->create();

        // 3. Active Positions (12% - 6 checklists)
        Checklist::factory()->count(6)->withActivePosition()->create();

        // 4. Completed Trades (33% - 16 checklists)
        Checklist::factory()->count(16)->withCompletedTrade()->create();

        // 5. Cancelled Trades (5% - 3 checklists)
        Checklist::factory()->count(3)->withCancelledTrade()->create();

        // Total: 50 checklists with realistic distribution
    }
}
