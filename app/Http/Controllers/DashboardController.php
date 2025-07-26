<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use Illuminate\Http\Request;
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
        $totalChecklists = Checklist::count();

        // Checklists this week
        $weeklyChecklists = Checklist::where('created_at', '>=', $startOfWeek)->count();

        // Checklists this month
        $monthlyChecklists = Checklist::where('created_at', '>=', $startOfMonth)->count();

        // Recent activity (last 7 days)
        $recentActivity = Checklist::where('created_at', '>=', $now->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($checklist) {
                return [
                    'id' => $checklist->id,
                    'symbol' => $checklist->symbol ?? $checklist->asset ?? 'Unknown',
                    'bias' => $checklist->bias ?? 'N/A',
                    'overall_score' => $checklist->score ?? 0, // Use original 0-100 score
                    'created_at' => $checklist->created_at->format('M j, Y g:i A'),
                    'has_trade_entry' => $checklist->status === 'executed'
                ];
            });

        // Weekly checklist trend (last 4 weeks)
        $weeklyTrend = [];
        for ($i = 3; $i >= 0; $i--) {
            $weekStart = $now->copy()->subWeeks($i)->startOfWeek();
            $weekEnd = $weekStart->copy()->endOfWeek();

            $count = Checklist::whereBetween('created_at', [$weekStart, $weekEnd])->count();

            $weeklyTrend[] = [
                'week' => $weekStart->format('M j'),
                'count' => $count
            ];
        }

        // Performance by bias (Long vs Short)
        $biasPerfomance = Checklist::select('bias', DB::raw('count(*) as count'), DB::raw('avg(score) as avg_score'))
            ->whereNotNull('bias')
            ->groupBy('bias')
            ->get()
            ->map(function ($item) {
                return [
                    'bias' => $item->bias,
                    'count' => $item->count,
                    'avg_score' => round($item->avg_score, 1)
                ];
            });

        // Top performing symbols (by score)
        $topSymbols = Checklist::select(
            DB::raw('COALESCE(symbol, asset) as symbol_key'),
            DB::raw('count(*) as count'),
            DB::raw('avg(score) as avg_score')
        )
            ->whereNotNull(DB::raw('COALESCE(symbol, asset)'))
            ->groupBy(DB::raw('COALESCE(symbol, asset)'))
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
            ->groupBy('score_range')
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
                'avg_score' => round(Checklist::avg('score') ?? 0, 1)
            ],
            'recent_activity' => $recentActivity,
            'weekly_trend' => $weeklyTrend,
            'bias_performance' => $biasPerfomance,
            'top_symbols' => $topSymbols,
            'score_distribution' => $scoreDistribution
        ];
    }
}
