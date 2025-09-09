<?php

namespace Database\Seeders;

use App\Models\TradeEntry;
use App\Models\TradeManagementItem;
use Illuminate\Database\Seeder;

class TradeManagementItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = 1; // Replace with actual user ID when auth is implemented

        // Get all trade entries that are pending or active
        $activeTradeEntries = TradeEntry::where('user_id', $userId)
            ->whereIn('trade_status', ['pending', 'active'])
            ->get();

        foreach ($activeTradeEntries as $tradeEntry) {
            // Create 2-5 management items per active trade
            $itemCount = rand(2, 5);

            TradeManagementItem::factory()
                ->count($itemCount)
                ->create([
                    'trade_entry_id' => $tradeEntry->id,
                    'user_id' => $userId,
                ]);

            // Ensure each trade has at least one high priority or critical item
            TradeManagementItem::factory()
                ->critical()
                ->create([
                    'trade_entry_id' => $tradeEntry->id,
                    'user_id' => $userId,
                    'title' => 'Move Stop to Breakeven',
                    'description' => 'Trade has reached 1R profit, consider moving stop to breakeven',
                    'type' => 'risk',
                    'due_date' => now()->addHours(rand(1, 48)),
                ]);
        }

        // Add some management items to closed trades as well (for demonstration)
        $closedTradeEntries = TradeEntry::where('user_id', $userId)
            ->whereIn('trade_status', ['win', 'loss', 'breakeven'])
            ->take(3)
            ->get();

        foreach ($closedTradeEntries as $tradeEntry) {
            // Create fewer items for closed trades, mostly completed
            TradeManagementItem::factory()
                ->completed()
                ->count(rand(1, 3))
                ->create([
                    'trade_entry_id' => $tradeEntry->id,
                    'user_id' => $userId,
                ]);
        }
    }
}
