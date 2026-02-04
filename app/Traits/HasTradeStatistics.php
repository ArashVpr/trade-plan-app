<?php

namespace App\Traits;

use App\Models\Checklist;
use App\Models\TradeEntry;
use Illuminate\Support\Facades\Auth;

trait HasTradeStatistics
{
    /**
     * Get comprehensive trade statistics for the authenticated user.
     *
     * @return array<string, int>
     */
    protected function getUserTradeStatistics(): array
    {
        $userId = Auth::id();

        // Base query for trade entries belonging to user's checklists
        $baseQuery = TradeEntry::whereHas('checklist', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });

        return [
            'total_checklists' => Checklist::where('user_id', $userId)->count(),
            'analysis_only' => Checklist::where('user_id', $userId)
                ->doesntHave('tradeEntry')
                ->count(),
            'pending' => (clone $baseQuery)->where('trade_status', 'pending')->count(),
            'active' => (clone $baseQuery)->where('trade_status', 'active')->count(),
            'wins' => (clone $baseQuery)->where('trade_status', 'win')->count(),
            'losses' => (clone $baseQuery)->where('trade_status', 'loss')->count(),
            'breakeven' => (clone $baseQuery)->where('trade_status', 'breakeven')->count(),
            'cancelled' => (clone $baseQuery)->where('trade_status', 'cancelled')->count(),
        ];
    }

    /**
     * Get specific trade status count for the authenticated user.
     */
    protected function getTradeStatusCount(string $status): int
    {
        return TradeEntry::whereHas('checklist', function ($q) {
            $q->where('user_id', Auth::id());
        })->where('trade_status', $status)->count();
    }
}
