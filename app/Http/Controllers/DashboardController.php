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

        // Score to Trade Outcome Analysis
        $scoreOutcomeAnalysis = DB::table('checklists')
            ->join('trade_entries', 'checklists.id', '=', 'trade_entries.checklist_id')
            ->select(
                DB::raw('CASE 
                    WHEN checklists.score >= 80 THEN "80-100"
                    WHEN checklists.score >= 60 THEN "60-79"
                    WHEN checklists.score >= 40 THEN "40-59"
                    ELSE "0-39"
                END as score_range'),
                DB::raw('count(*) as total_trades'),
                DB::raw('ROUND(AVG(CASE WHEN trade_entries.trade_status = "win" THEN 1 ELSE 0 END) * 100, 1) as win_rate'),
                DB::raw('ROUND(AVG(CASE WHEN trade_entries.trade_status IN ("win", "loss") THEN trade_entries.rrr ELSE NULL END), 2) as avg_return')
            )
            ->where('checklists.user_id', Auth::id())
            ->whereIn('trade_entries.trade_status', ['win', 'loss', 'breakeven'])
            ->groupBy(DB::raw('CASE 
                WHEN checklists.score >= 80 THEN "80-100"
                WHEN checklists.score >= 60 THEN "60-79"
                WHEN checklists.score >= 40 THEN "40-59"
                ELSE "0-39"
            END'))
            ->orderBy(DB::raw('CASE 
                WHEN score_range = "0-39" THEN 1
                WHEN score_range = "40-59" THEN 2
                WHEN score_range = "60-79" THEN 3
                WHEN score_range = "80-100" THEN 4
            END'), 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'score_range' => $item->score_range,
                    'total_trades' => $item->total_trades,
                    'win_rate' => $item->win_rate ?? 0,
                    'avg_return' => $item->avg_return ?? 0
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

        // Setup Performance Analysis
        $setupPerformanceAnalysis = $this->getSetupPerformanceAnalysis();

        // Winning Trades with Setups
        $winningTrades = $this->getWinningTradesWithSetups();

        return [
            'overview' => [
                'total_checklists' => $totalChecklists,
                'weekly_checklists' => $weeklyChecklists,
                'monthly_checklists' => $monthlyChecklists,
                'avg_score' => round(Checklist::where('user_id', Auth::id())->avg('score') ?? 0, 1)
            ],
            'recent_activity' => $recentActivity,
            'weekly_trend' => $weeklyTrend,
            'score_outcome_analysis' => $scoreOutcomeAnalysis,
            'setup_performance_analysis' => $setupPerformanceAnalysis,
            'winning_trades' => $winningTrades,
            'top_symbols' => $topSymbols,
            'score_distribution' => $scoreDistribution
        ];
    }

    /**
     * Analyze setup performance - which technical/fundamental combinations perform best
     */
    private function getSetupPerformanceAnalysis()
    {
        // Get completed trades with their checklist setups
        $setupData = DB::table('checklists')
            ->join('trade_entries', 'checklists.id', '=', 'trade_entries.checklist_id')
            ->select(
                'checklists.technicals',
                'checklists.fundamentals',
                'trade_entries.trade_status',
                'trade_entries.rrr'
            )
            ->where('checklists.user_id', Auth::id())
            ->whereIn('trade_entries.trade_status', ['win', 'loss', 'breakeven'])
            ->get();

        // Analyze technical setups
        $technicalPerformance = [];
        $fundamentalPerformance = [];
        $combinedPerformance = [];

        foreach ($setupData as $trade) {
            $technicals = json_decode($trade->technicals, true);
            $fundamentals = json_decode($trade->fundamentals, true);
            $isWin = $trade->trade_status === 'win';

            // Technical Location Analysis
            if (isset($technicals['location'])) {
                $location = $technicals['location'];
                if (!isset($technicalPerformance[$location])) {
                    $technicalPerformance[$location] = ['total' => 0, 'wins' => 0, 'total_rrr' => 0];
                }
                $technicalPerformance[$location]['total']++;
                if ($isWin) $technicalPerformance[$location]['wins']++;
                $technicalPerformance[$location]['total_rrr'] += $trade->rrr ?? 0;
            }

            // Technical Direction Analysis
            if (isset($technicals['direction'])) {
                $direction = $technicals['direction'];
                if (!isset($technicalPerformance[$direction])) {
                    $technicalPerformance[$direction] = ['total' => 0, 'wins' => 0, 'total_rrr' => 0];
                }
                $technicalPerformance[$direction]['total']++;
                if ($isWin) $technicalPerformance[$direction]['wins']++;
                $technicalPerformance[$direction]['total_rrr'] += $trade->rrr ?? 0;
            }

            // Fundamental Analysis
            if (isset($fundamentals['valuation'])) {
                $valuation = $fundamentals['valuation'];
                if (!isset($fundamentalPerformance[$valuation])) {
                    $fundamentalPerformance[$valuation] = ['total' => 0, 'wins' => 0, 'total_rrr' => 0];
                }
                $fundamentalPerformance[$valuation]['total']++;
                if ($isWin) $fundamentalPerformance[$valuation]['wins']++;
                $fundamentalPerformance[$valuation]['total_rrr'] += $trade->rrr ?? 0;
            }

            // Combined Setup Analysis (Location + Direction + Valuation)
            if (isset($technicals['location']) && isset($technicals['direction']) && isset($fundamentals['valuation'])) {
                $combinedKey = $technicals['location'] . ' + ' . $technicals['direction'] . ' + ' . $fundamentals['valuation'];
                if (!isset($combinedPerformance[$combinedKey])) {
                    $combinedPerformance[$combinedKey] = ['total' => 0, 'wins' => 0, 'total_rrr' => 0];
                }
                $combinedPerformance[$combinedKey]['total']++;
                if ($isWin) $combinedPerformance[$combinedKey]['wins']++;
                $combinedPerformance[$combinedKey]['total_rrr'] += $trade->rrr ?? 0;
            }
        }

        // Calculate win rates and average RRR
        $processPerformanceData = function ($data) {
            $result = [];
            foreach ($data as $setup => $stats) {
                if ($stats['total'] >= 2) { // Only include setups with at least 2 trades
                    $result[] = [
                        'setup_name' => $setup,
                        'total_trades' => $stats['total'],
                        'win_rate' => round(($stats['wins'] / $stats['total']) * 100, 1),
                        'avg_rrr' => round($stats['total_rrr'] / $stats['total'], 2)
                    ];
                }
            }
            // Sort by win rate descending
            usort($result, function ($a, $b) {
                return $b['win_rate'] <=> $a['win_rate'];
            });
            return array_slice($result, 0, 5); // Top 5
        };

        return [
            'technical_setups' => $processPerformanceData($technicalPerformance),
            'fundamental_setups' => $processPerformanceData($fundamentalPerformance),
            'combined_setups' => $processPerformanceData($combinedPerformance)
        ];
    }

    /**
     * Get winning trades with their complete checklist setups
     */
    private function getWinningTradesWithSetups()
    {
        return Checklist::with('tradeEntry')
            ->where('user_id', Auth::id())
            ->whereHas('tradeEntry', function ($query) {
                $query->where('trade_status', 'win');
            })
            ->orderBy('created_at', 'desc')
            ->get() // Show ALL winning trades
            ->map(function ($checklist) {
                $tradeEntry = $checklist->tradeEntry;
                $technicals = $checklist->technicals;
                $fundamentals = $checklist->fundamentals;
                $zoneQualifiers = $checklist->zone_qualifiers ?? [];

                return [
                    'id' => $checklist->id,
                    'symbol' => $checklist->symbol,
                    'score' => $checklist->score,
                    'position_type' => $tradeEntry->position_type,
                    'entry_price' => $tradeEntry->entry_price,
                    'target_price' => $tradeEntry->target_price,
                    'rrr' => $tradeEntry->rrr,
                    'entry_date' => $tradeEntry->entry_date,
                    'created_at' => $checklist->created_at->format('M j, Y'),

                    // Setup Details
                    'technical_location' => $technicals['location'] ?? 'N/A',
                    'technical_direction' => $technicals['direction'] ?? 'N/A',
                    'fundamental_valuation' => $fundamentals['valuation'] ?? 'N/A',
                    'fundamental_seasonal' => $fundamentals['seasonalConfluence'] ?? 'N/A',
                    'fundamental_noncommercials' => $fundamentals['nonCommercials'] ?? 'N/A',
                    'fundamental_cot_index' => $fundamentals['cotIndex'] ?? 'N/A',
                    'zone_qualifiers' => $zoneQualifiers,
                    'zone_qualifiers_count' => count($zoneQualifiers),

                    // Combined setup description
                    'setup_summary' => $this->generateSetupSummary($technicals, $fundamentals, $zoneQualifiers)
                ];
            });
    }

    /**
     * Generate a readable setup summary
     */
    private function generateSetupSummary($technicals, $fundamentals, $zoneQualifiers)
    {
        $summary = [];

        // Technical summary
        if (isset($technicals['location']) && isset($technicals['direction'])) {
            $summary[] = $technicals['location'] . ' ' . $technicals['direction'];
        }

        // Fundamental summary - include all fundamental fields
        $fundamentalParts = [];
        if (isset($fundamentals['valuation']) && $fundamentals['valuation'] !== 'Neutral') {
            $fundamentalParts[] = $fundamentals['valuation'];
        }
        if (isset($fundamentals['seasonalConfluence']) && $fundamentals['seasonalConfluence'] === 'Yes') {
            $fundamentalParts[] = 'Seasonal';
        }
        if (isset($fundamentals['nonCommercials']) && $fundamentals['nonCommercials'] === 'Divergence') {
            $fundamentalParts[] = 'COT Divergence';
        }
        if (isset($fundamentals['cotIndex']) && $fundamentals['cotIndex'] !== 'Neutral') {
            $fundamentalParts[] = $fundamentals['cotIndex'] . ' COT';
        }

        if (!empty($fundamentalParts)) {
            $summary[] = implode(' & ', $fundamentalParts);
        }

        // Zone qualifiers summary
        if (!empty($zoneQualifiers)) {
            $summary[] = count($zoneQualifiers) . ' Zone Qualifiers';
        }

        return implode(' + ', $summary);
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
