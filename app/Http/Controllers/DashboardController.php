<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get dashboard statistics
        $stats = $this->getDashboardStats();

        return Inertia::render('Dashboard', [
            'stats' => $stats
        ]);
    }

    private function getDashboardStats()
    {
        $now = Carbon::now();
        $startOfWeek = $now->startOfWeek();
        $startOfMonth = $now->startOfMonth();

        // Total checklists
        $totalChecklists = Checklist::where('user_id', Auth::id())->count();

        // Checklists this week
        $weeklyChecklists = Checklist::where('user_id', Auth::id())
            ->where('created_at', '>=', $startOfWeek)->count();

        // Checklists this month
        $monthlyChecklists = Checklist::where('user_id', Auth::id())
            ->where('created_at', '>=', $startOfMonth)->count();

        // Recent activity (last 7 days)
        $recentActivity = Checklist::with('tradeEntry')
            ->where('user_id', Auth::id())
            ->where('created_at', '>=', $now->copy()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($checklist) {
                $tradeEntry = $checklist->tradeEntry;

                return [
                    'id' => $checklist->id,
                    'symbol' => $checklist->symbol,
                    'position_type' => $tradeEntry ? ucfirst($tradeEntry->position_type) : 'N/A',
                    'overall_score' => $checklist->score ?? 0,
                    'created_at' => $checklist->created_at->format('M j, Y g:i A'),
                    'trade_status' => $this->getTradeStatus($tradeEntry),
                    'status_severity' => $this->getStatusSeverity($tradeEntry)
                ];
            });

        // Weekly checklist trend (last 4 weeks)
        $weeklyTrend = [];
        $currentWeek = Carbon::now(); // Fresh instance for weekly calculations
        for ($i = 3; $i >= 0; $i--) {
            $weekStart = $currentWeek->copy()->subWeeks($i)->startOfWeek();
            $weekEnd = $weekStart->copy()->endOfWeek();
            $count = Checklist::where('user_id', Auth::id())
                ->whereBetween('created_at', [$weekStart, $weekEnd])->count();

            $weeklyTrend[] = [
                'week' => $weekStart->format('M j'),
                'count' => $count
            ];
        }

        // Performance by position type (Long vs Short) - from trade entries
        $positionTypePerformance = DB::table('checklists')
            ->join('trade_entries', 'checklists.id', '=', 'trade_entries.checklist_id')
            ->select(
                'trade_entries.position_type',
                DB::raw('count(*) as count'),
                DB::raw('avg(checklists.score) as avg_score')
            )
            ->where('checklists.user_id', Auth::id())
            ->whereNotNull('trade_entries.position_type')
            ->groupBy('trade_entries.position_type')
            ->get()
            ->map(function ($item) {
                return [
                    'position_type' => ucfirst($item->position_type), // Capitalize Long/Short
                    'count' => $item->count,
                    'avg_score' => round($item->avg_score, 1)
                ];
            });

        // Top performing symbols (by score)
        $topSymbols = Checklist::select(
            DB::raw('COALESCE(symbol) as symbol_key'),
            DB::raw('count(*) as count'),
            DB::raw('avg(score) as avg_score')
        )
            ->where('user_id', Auth::id())
            ->whereNotNull(DB::raw('COALESCE(symbol)'))
            ->groupBy(DB::raw('COALESCE(symbol)'))
            ->having('count', '>=', 1) // Show all symbols for now
            ->orderBy('avg_score', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'symbol' => $item->symbol_key ?? 'Unknown',
                    'count' => $item->count,
                    'avg_score' => round($item->avg_score, 1)
                ];
            });

        // Score distribution
        $scoreDistribution = Checklist::select(
            DB::raw('CASE 
                WHEN score >= 80 THEN "Excellent (80-100)"
                WHEN score >= 60 THEN "Good (60-79)"
                WHEN score >= 40 THEN "Average (40-59)"
                ELSE "Poor (0-39)"
            END as score_range'),
            DB::raw('count(*) as count')
        )
            ->where('user_id', Auth::id())
            ->groupBy(DB::raw('CASE 
                WHEN score >= 80 THEN "Excellent (80-100)"
                WHEN score >= 60 THEN "Good (60-79)"
                WHEN score >= 40 THEN "Average (40-59)"
                ELSE "Poor (0-39)"
            END'))
            ->orderBy(DB::raw('CASE 
                WHEN score_range = "Poor (0-39)" THEN 1
                WHEN score_range = "Average (40-59)" THEN 2
                WHEN score_range = "Good (60-79)" THEN 3
                WHEN score_range = "Excellent (80-100)" THEN 4
            END'), 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'range' => $item->score_range,
                    'count' => $item->count
                ];
            });

        return [
            'overview' => [
                'total_checklists' => $totalChecklists,
                'weekly_checklists' => $weeklyChecklists,
                'monthly_checklists' => $monthlyChecklists,
                'avg_score' => round(Checklist::where('user_id', Auth::id())->avg('score') ?? 0, 1)
            ],
            'recent_activity' => $recentActivity,
            'weekly_trend' => $weeklyTrend,
            'position_type_performance' => $positionTypePerformance,
            'top_symbols' => $topSymbols,
            'score_distribution' => $scoreDistribution
        ];
    }

    /**
     * Get human-readable trade status
     */
    private function getTradeStatus($tradeEntry)
    {
        if (!$tradeEntry) {
            return 'Analysis Only';
        }

        // Check if we have the new trade_status field (after migration)
        if (isset($tradeEntry->trade_status)) {
            return match ($tradeEntry->trade_status) {
                'pending' => 'Order Pending',
                'active' => 'Position Open',
                'win' => 'Win',
                'loss' => 'Loss',
                'breakeven' => 'Breakeven',
                'cancelled' => 'Cancelled',
                default => 'Unknown'
            };
        }

        return 'Trade Pending';
    }

    /**
     * Get PrimeVue severity for trade status
     */
    private function getStatusSeverity($tradeEntry)
    {
        if (!$tradeEntry) {
            return 'info'; // Blue for analysis only
        }

        // Check if we have the new trade_status field
        if (isset($tradeEntry->trade_status)) {
            return match ($tradeEntry->trade_status) {
                'pending' => 'warn',   // Yellow  
                'active' => 'warn',    // Yellow
                'win' => 'success',     // Green
                'loss' => 'danger',     // Red  
                'breakeven' => 'warn', // Yellow
                'cancelled' => 'secondary', // Gray
                default => 'secondary'
            };
        }

        return 'warn'; // Yellow for pending
    }
}
