<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\ChecklistWeights;
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

        // Pattern Analysis
        $patternAnalysis = $this->analyzeWinningPatterns($this->getWinningTradesWithSetups());

        return [
            'overview' => [
                'total_checklists' => $totalChecklists,
                'weekly_checklists' => $weeklyChecklists,
                'monthly_checklists' => $monthlyChecklists,
                'avg_score' => round(Checklist::where('user_id', Auth::id())->avg('score') ?? 0, 1)
            ],
            'recent_activity' => $recentActivity,
            'weekly_trend' => $weeklyTrend,
            'setup_performance_analysis' => $setupPerformanceAnalysis,
            'pattern_analysis' => $patternAnalysis,
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
     * Calculate directional bias based on checklist inputs
     */
    private function calculateDirectionalBias($trade, $settings)
    {
        // Get signal values (same logic as JavaScript composable)
        $getSignalValue = function ($category, $selection) {
            if (!$selection || $selection === 'N/A') return 0;

            switch ($category) {
                case 'location':
                    return [
                        'Very Cheap' => +1,
                        'Cheap' => +1,
                        'EQ' => 0,
                        'Expensive' => -1,
                        'Very Expensive' => -1
                    ][$selection] ?? 0;

                case 'valuation':
                    return [
                        'Undervalued' => +1,
                        'Neutral' => 0,
                        'Overvalued' => -1
                    ][$selection] ?? 0;

                case 'seasonality':
                    return [
                        'Bullish' => +1,
                        'Neutral' => 0,
                        'Bearish' => -1
                    ][$selection] ?? 0;

                case 'nonCommercials':
                    return [
                        'Bullish Divergence' => +1,
                        'Neutral' => 0,
                        'Bearish Divergence' => -1
                    ][$selection] ?? 0;

                case 'cotIndex':
                    return [
                        'Bullish' => +1,
                        'Neutral' => 0,
                        'Bearish' => -1
                    ][$selection] ?? 0;

                default:
                    return 0;
            }
        };

        // Get signal values from trade data
        $techValue = $getSignalValue('location', $trade['technical_location']);
        $valuationValue = $getSignalValue('valuation', $trade['fundamental_valuation']);
        $seasonalityValue = $getSignalValue('seasonality', $trade['fundamental_seasonal']);
        $nonCommercialsValue = $getSignalValue('nonCommercials', $trade['fundamental_noncommercials']);
        $cotValue = $getSignalValue('cotIndex', $trade['fundamental_cot_index']);

        // Get weights from settings
        $techWeight = $techValue !== 0 ? (
            in_array($trade['technical_location'], ['Very Cheap', 'Very Expensive'])
            ? floatval($settings->technical_very_exp_chp_weight ?? 0)
            : floatval($settings->technical_exp_chp_weight ?? 0)
        ) : 0;

        $valuationWeight = floatval($settings->fundamental_valuation_weight ?? 0);
        $seasonalityWeight = floatval($settings->fundamental_seasonal_weight ?? 0);
        $nonCommercialsWeight = floatval($settings->fundamental_noncommercial_divergence_weight ?? 0);
        $cotWeight = floatval($settings->fundamental_cot_index_weight ?? 0);

        // Calculate weighted contributions
        $techContribution = $techValue * $techWeight;
        $valuationContribution = $valuationValue * $valuationWeight;
        $seasonalityContribution = $seasonalityValue * $seasonalityWeight;
        $nonCommercialsContribution = $nonCommercialsValue * $nonCommercialsWeight;
        $cotContribution = $cotValue * $cotWeight;

        $weightedSum = $techContribution + $valuationContribution + $seasonalityContribution +
            $nonCommercialsContribution + $cotContribution;

        // Calculate maximum possible weighted sum for confidence calculation
        $maxTechWeight = max(
            floatval($settings->technical_very_exp_chp_weight ?? 0),
            floatval($settings->technical_exp_chp_weight ?? 0)
        );

        $maxSum = (1 * $maxTechWeight) + $valuationWeight + $seasonalityWeight +
            $nonCommercialsWeight + $cotWeight;

        // Determine bias and confidence
        $bias = $weightedSum > 0 ? 'Bullish' : ($weightedSum < 0 ? 'Bearish' : 'Neutral');
        $confidence = $maxSum > 0 ? round((abs($weightedSum) / $maxSum) * 100) : 0;

        // Determine strength
        $strength = 'Weak';
        if ($confidence >= 81) $strength = 'Strong';
        elseif ($confidence >= 61) $strength = 'Moderate';

        return [
            'bias' => $bias,
            'confidence' => $confidence,
            'strength' => $strength,
            'weighted_sum' => round($weightedSum, 1),
            'max_sum' => round($maxSum, 1)
        ];
    }

    /**
     * Get bias display name following checklist naming convention
     */
    private function getBiasDisplayName($bias, $strength)
    {
        if ($bias === 'Bullish') {
            switch ($strength) {
                case 'Strong':
                    return 'Strong Buy';
                case 'Moderate':
                    return 'Buy';
                case 'Weak':
                    return 'Lean Buy';
                default:
                    return 'Buy';
            }
        } elseif ($bias === 'Bearish') {
            switch ($strength) {
                case 'Strong':
                    return 'Strong Sell';
                case 'Moderate':
                    return 'Sell';
                case 'Weak':
                    return 'Lean Sell';
                default:
                    return 'Sell';
            }
        }

        return 'Neutral';
    }

    /**
     * Analyze patterns in winning trades to identify what consistently works
     */
    private function analyzeWinningPatterns($winningTrades)
    {
        if ($winningTrades->isEmpty()) {
            return [
                'most_profitable_setups' => [],
                'score_patterns' => [],
                'symbol_patterns' => [],
                'time_patterns' => [],
                'zone_patterns' => [],
                'technical_patterns' => [],
                'fundamental_patterns' => [],
                'directional_bias_patterns' => [],
                'alignment_analysis' => [
                    'zones_focused' => 0,
                    'technicals_focused' => 0,
                    'fundamentals_focused' => 0,
                    'balanced' => 0
                ],
                'recommendations' => [],
                'total_wins' => 0
            ];
        }

        $totalWins = $winningTrades->count();

        // Get user's checklist weights for directional bias calculation
        $settings = ChecklistWeights::where('user_id', Auth::id())->first();
        if (!$settings) {
            // Default weights if none set
            $settings = new ChecklistWeights();
        }

        // Initialize counters for patterns
        $setupFrequency = [];
        $scorePatterns = ['80+' => 0, '60-79' => 0, '40-59' => 0, '<40' => 0];
        $symbolFrequency = [];
        $timePatterns = [];
        $zonePatterns = [];

        // Track which trades have each pattern (for percentage calculation)
        $technicalPatternTrades = [];
        $fundamentalPatternTrades = [];
        $directionalBiasPatternTrades = [];

        // Track alignment towards zones, technicals, or fundamentals
        $alignmentAnalysis = [
            'zones_focused' => 0,
            'technicals_focused' => 0,
            'fundamentals_focused' => 0,
            'balanced' => 0
        ];

        foreach ($winningTrades as $trade) {
            $tradeId = $trade['id'];

            // Setup combination frequency
            $setupKey = $trade['setup_summary'];
            if (!isset($setupFrequency[$setupKey])) {
                $setupFrequency[$setupKey] = [
                    'count' => 0,
                    'total_rrr' => 0,
                    'avg_score' => 0,
                    'directional_bias_data' => []
                ];
            }
            $setupFrequency[$setupKey]['count']++;
            $setupFrequency[$setupKey]['total_rrr'] += $trade['rrr'] ?? 0;
            $setupFrequency[$setupKey]['avg_score'] += $trade['score'] ?? 0;

            // Calculate directional bias for this trade and add to setup data
            $biasData = $this->calculateDirectionalBias($trade, $settings);
            $setupFrequency[$setupKey]['directional_bias_data'][] = [
                'bias' => $biasData['bias'],
                'strength' => $biasData['strength'],
                'confidence' => $biasData['confidence']
            ];

            // Score patterns
            $score = $trade['score'] ?? 0;
            if ($score >= 80) $scorePatterns['80+']++;
            elseif ($score >= 60) $scorePatterns['60-79']++;
            elseif ($score >= 40) $scorePatterns['40-59']++;
            else $scorePatterns['<40']++;

            // Symbol patterns
            $symbol = $trade['symbol'] ?? 'Unknown';
            $symbolFrequency[$symbol] = ($symbolFrequency[$symbol] ?? 0) + 1;

            // Time patterns (month analysis)
            $month = date('M', strtotime($trade['created_at']));
            $timePatterns[$month] = ($timePatterns[$month] ?? 0) + 1;

            // Zone qualifier patterns
            $zoneCount = $trade['zone_qualifiers_count'] ?? 0;
            $zoneKey = $zoneCount === 0 ? '0 Zones' : ($zoneCount <= 2 ? '1-2 Zones' : ($zoneCount <= 4 ? '3-4 Zones' : '5+ Zones'));
            $zonePatterns[$zoneKey] = ($zonePatterns[$zoneKey] ?? 0) + 1;

            // Directional bias patterns
            $biasData = $this->calculateDirectionalBias($trade, $settings);
            $bias = $biasData['bias'];
            $confidence = $biasData['confidence'];
            $strength = $biasData['strength'];

            if ($bias !== 'Neutral') {
                // Track overall bias patterns with proper naming
                $biasKey = $bias;
                $directionalBiasPatternTrades[$biasKey][] = $tradeId;

                // Track bias with strength using checklist naming convention
                $biasStrengthKey = $this->getBiasDisplayName($bias, $strength);
                $directionalBiasPatternTrades[$biasStrengthKey][] = $tradeId;

                // Track bias with confidence ranges using checklist naming
                if ($confidence >= 80) {
                    $highConfidenceKey = $this->getBiasDisplayName($bias, 'Strong');
                    $directionalBiasPatternTrades[$highConfidenceKey][] = $tradeId;
                } elseif ($confidence >= 60) {
                    $mediumConfidenceKey = $this->getBiasDisplayName($bias, 'Moderate');
                    $directionalBiasPatternTrades[$mediumConfidenceKey][] = $tradeId;
                } else {
                    $lowConfidenceKey = $this->getBiasDisplayName($bias, 'Weak');
                    $directionalBiasPatternTrades[$lowConfidenceKey][] = $tradeId;
                }
            } else {
                $directionalBiasPatternTrades['Neutral'][] = $tradeId;
            }

            // Technical patterns - track trades, not occurrences
            $techLocation = $trade['technical_location'] ?? 'N/A';
            $techDirection = $trade['technical_direction'] ?? 'N/A';

            if ($techLocation !== 'N/A') {
                // Group extreme zones together
                if (in_array($techLocation, ['Very Expensive', 'Very Cheap'])) {
                    $technicalPatternTrades['Location: Extreme Zones'][] = $tradeId;
                } elseif (in_array($techLocation, ['Expensive', 'Cheap'])) {
                    $technicalPatternTrades['Location: Moderate Zones'][] = $tradeId;
                } else {
                    $technicalPatternTrades['Location: ' . $techLocation][] = $tradeId;
                }
            }
            if ($techDirection !== 'N/A') {
                $technicalPatternTrades['Direction: ' . $techDirection][] = $tradeId;
            }

            // Fundamental patterns - track trades, not occurrences
            $fundValuation = $trade['fundamental_valuation'] ?? 'N/A';
            $fundSeasonal = $trade['fundamental_seasonal'] ?? 'N/A';
            $fundNonCommercials = $trade['fundamental_noncommercials'] ?? 'N/A';
            $fundCotIndex = $trade['fundamental_cot_index'] ?? 'N/A';

            if ($fundValuation !== 'N/A' && $fundValuation !== 'Neutral') {
                // Group valuation categories
                if (in_array($fundValuation, ['Overvalued', 'Undervalued'])) {
                    $fundamentalPatternTrades['Valuation: Extreme Levels'][] = $tradeId;
                } else {
                    $fundamentalPatternTrades['Valuation: ' . $fundValuation][] = $tradeId;
                }
            }
            if ($fundSeasonal === 'Yes') {
                $fundamentalPatternTrades['Seasonal Confluence'][] = $tradeId;
            }
            if ($fundNonCommercials === 'Divergence') {
                $fundamentalPatternTrades['COT Divergence'][] = $tradeId;
            }
            if ($fundCotIndex !== 'N/A' && $fundCotIndex !== 'Neutral') {
                // Group COT Index categories  
                if (in_array($fundCotIndex, ['Bullish', 'Bearish'])) {
                    $fundamentalPatternTrades['COT Index: Directional'][] = $tradeId;
                } else {
                    $fundamentalPatternTrades['COT Index: ' . $fundCotIndex][] = $tradeId;
                }
            }

            // Calculate alignment for this trade
            $zoneStrength = $trade['zone_qualifiers_count'] ?? 0;

            // Technical strength (location + direction)
            $technicalStrength = 0;
            if (($trade['technical_location'] ?? 'N/A') !== 'N/A') $technicalStrength++;
            if (($trade['technical_direction'] ?? 'N/A') !== 'N/A') $technicalStrength++;

            // Fundamental strength (valuation + seasonal + COT factors)
            $fundamentalStrength = 0;
            if (($trade['fundamental_valuation'] ?? 'N/A') !== 'N/A' && $trade['fundamental_valuation'] !== 'Neutral') $fundamentalStrength++;
            if (($trade['fundamental_seasonal'] ?? 'N/A') === 'Yes') $fundamentalStrength++;
            if (($trade['fundamental_noncommercials'] ?? 'N/A') === 'Divergence') $fundamentalStrength++;
            if (($trade['fundamental_cot_index'] ?? 'N/A') !== 'N/A' && $trade['fundamental_cot_index'] !== 'Neutral') $fundamentalStrength++;

            // Determine primary alignment
            $maxStrength = max($zoneStrength, $technicalStrength, $fundamentalStrength);
            $strengthCounts = array_count_values([$zoneStrength, $technicalStrength, $fundamentalStrength]);

            if ($strengthCounts[$maxStrength] > 1) {
                // Balanced - multiple categories tied for highest
                $alignmentAnalysis['balanced']++;
            } elseif ($zoneStrength === $maxStrength && $zoneStrength > 0) {
                $alignmentAnalysis['zones_focused']++;
            } elseif ($technicalStrength === $maxStrength && $technicalStrength > 0) {
                $alignmentAnalysis['technicals_focused']++;
            } elseif ($fundamentalStrength === $maxStrength && $fundamentalStrength > 0) {
                $alignmentAnalysis['fundamentals_focused']++;
            } else {
                // Edge case - all zeros, count as balanced
                $alignmentAnalysis['balanced']++;
            }
        }

        // Convert technical patterns to count and percentage
        $technicalPatterns = [];
        foreach ($technicalPatternTrades as $pattern => $tradeIds) {
            $uniqueTradesCount = count(array_unique($tradeIds));
            $technicalPatterns[$pattern] = [
                'count' => $uniqueTradesCount,
                'percentage' => round(($uniqueTradesCount / $totalWins) * 100, 1)
            ];
        }

        // Convert fundamental patterns to count and percentage
        $fundamentalPatterns = [];
        foreach ($fundamentalPatternTrades as $pattern => $tradeIds) {
            $uniqueTradesCount = count(array_unique($tradeIds));
            $fundamentalPatterns[$pattern] = [
                'count' => $uniqueTradesCount,
                'percentage' => round(($uniqueTradesCount / $totalWins) * 100, 1)
            ];
        }

        // Convert directional bias patterns to count and percentage
        $directionalBiasPatterns = [];
        foreach ($directionalBiasPatternTrades as $pattern => $tradeIds) {
            $uniqueTradesCount = count(array_unique($tradeIds));
            $directionalBiasPatterns[$pattern] = [
                'count' => $uniqueTradesCount,
                'percentage' => round(($uniqueTradesCount / $totalWins) * 100, 1)
            ];
        }

        // Process most profitable setups
        $mostProfitableSetups = [];
        foreach ($setupFrequency as $setup => $data) {
            if ($data['count'] >= 1) { // Show all setups, even single occurrences
                // Process directional bias data for this setup
                $biasFrequency = [];
                $totalConfidence = 0;
                $dominantBias = 'Neutral';

                foreach ($data['directional_bias_data'] as $biasEntry) {
                    $biasKey = $this->getBiasDisplayName($biasEntry['bias'], $biasEntry['strength']);
                    $biasFrequency[$biasKey] = ($biasFrequency[$biasKey] ?? 0) + 1;
                    $totalConfidence += $biasEntry['confidence'];
                }

                // Find the most common bias for this setup
                if (!empty($biasFrequency)) {
                    arsort($biasFrequency);
                    $dominantBias = array_key_first($biasFrequency);
                }

                $avgConfidence = count($data['directional_bias_data']) > 0
                    ? round($totalConfidence / count($data['directional_bias_data']), 1)
                    : 0;

                $mostProfitableSetups[] = [
                    'setup' => $setup,
                    'frequency' => $data['count'],
                    'avg_rrr' => round($data['total_rrr'] / $data['count'], 2),
                    'avg_score' => round($data['avg_score'] / $data['count'], 1),
                    'success_rate' => round(($data['count'] / $totalWins) * 100, 1),
                    'dominant_bias' => $dominantBias,
                    'avg_bias_confidence' => $avgConfidence,
                    'bias_distribution' => $biasFrequency
                ];
            }
        }

        // Sort by frequency descending
        usort($mostProfitableSetups, function ($a, $b) {
            return $b['frequency'] <=> $a['frequency'];
        });

        // Generate recommendations
        $recommendations = $this->generatePatternRecommendations($mostProfitableSetups, $scorePatterns, $symbolFrequency, $zonePatterns, $directionalBiasPatterns);

        return [
            'most_profitable_setups' => array_slice($mostProfitableSetups, 0, 5),
            'score_patterns' => $scorePatterns,
            'symbol_patterns' => array_slice(arsort($symbolFrequency) ? $symbolFrequency : [], 0, 5, true),
            'time_patterns' => $timePatterns,
            'zone_patterns' => $zonePatterns,
            'technical_patterns' => $technicalPatterns,
            'fundamental_patterns' => $fundamentalPatterns,
            'directional_bias_patterns' => $directionalBiasPatterns,
            'alignment_analysis' => $alignmentAnalysis,
            'recommendations' => $recommendations,
            'total_wins' => $totalWins
        ];
    }

    /**
     * Generate actionable recommendations based on winning patterns
     */
    private function generatePatternRecommendations($setups, $scorePatterns, $symbolFrequency, $zonePatterns, $directionalBiasPatterns)
    {
        $recommendations = [];

        // Setup-based recommendations
        if (!empty($setups)) {
            $topSetup = $setups[0];
            if ($topSetup['frequency'] >= 3) {
                $recommendations[] = [
                    'type' => 'setup',
                    'title' => 'Your Most Successful Setup',
                    'description' => "'{$topSetup['setup']}' has won {$topSetup['frequency']} times with avg R:R of {$topSetup['avg_rrr']}",
                    'action' => 'Focus on this setup pattern for future trades'
                ];
            }
        }

        // Score-based recommendations
        $totalScoreWins = array_sum($scorePatterns);
        if ($totalScoreWins > 0) {
            $highScorePercentage = round(($scorePatterns['80+'] / $totalScoreWins) * 100, 1);
            if ($highScorePercentage >= 60) {
                $recommendations[] = [
                    'type' => 'score',
                    'title' => 'High Score Pattern',
                    'description' => "{$highScorePercentage}% of your wins came from scores 80+",
                    'action' => 'Only trade setups with scores above 80 for higher win probability'
                ];
            } elseif ($scorePatterns['60-79'] > $scorePatterns['80+']) {
                $recommendations[] = [
                    'type' => 'score',
                    'title' => 'Sweet Spot Identified',
                    'description' => "Most wins come from 60-79 score range",
                    'action' => 'Your optimal trading zone appears to be 60-79 score range'
                ];
            }
        }

        // Symbol-based recommendations
        if (!empty($symbolFrequency)) {
            arsort($symbolFrequency);
            $topSymbol = array_key_first($symbolFrequency);
            $topSymbolWins = $symbolFrequency[$topSymbol];
            if ($topSymbolWins >= 3) {
                $recommendations[] = [
                    'type' => 'symbol',
                    'title' => 'Favorite Currency Pair',
                    'description' => "{$topSymbol} has generated {$topSymbolWins} winning trades",
                    'action' => 'Consider specializing in this pair - you understand its behavior well'
                ];
            }
        }

        // Zone-based recommendations
        if (!empty($zonePatterns)) {
            arsort($zonePatterns);
            $topZonePattern = array_key_first($zonePatterns);
            $zoneWins = $zonePatterns[$topZonePattern];
            if ($zoneWins >= 3) {
                $recommendations[] = [
                    'type' => 'zone',
                    'title' => 'Zone Qualifier Sweet Spot',
                    'description' => "Setups with {$topZonePattern} won {$zoneWins} times",
                    'action' => 'This zone qualifier combination seems optimal for your strategy'
                ];
            }
        }

        // Directional bias-based recommendations
        if (!empty($directionalBiasPatterns)) {
            $bullishWins = $directionalBiasPatterns['Bullish']['count'] ?? 0;
            $bearishWins = $directionalBiasPatterns['Bearish']['count'] ?? 0;
            $neutralWins = $directionalBiasPatterns['Neutral']['count'] ?? 0;
            $totalDirectionalWins = $bullishWins + $bearishWins + $neutralWins;

            if ($totalDirectionalWins > 0) {
                // Strong directional bias recommendations
                if ($bullishWins >= $bearishWins * 2 && $bullishWins >= 3) {
                    $bullishPercentage = round(($bullishWins / $totalDirectionalWins) * 100, 1);
                    $recommendations[] = [
                        'type' => 'directional_bias',
                        'title' => 'Bullish Specialist',
                        'description' => "{$bullishPercentage}% of your wins came from bullish setups ({$bullishWins} trades)",
                        'action' => 'Focus on oversold conditions and undervalued assets for your edge'
                    ];
                } elseif ($bearishWins >= $bullishWins * 2 && $bearishWins >= 3) {
                    $bearishPercentage = round(($bearishWins / $totalDirectionalWins) * 100, 1);
                    $recommendations[] = [
                        'type' => 'directional_bias',
                        'title' => 'Bearish Expert',
                        'description' => "{$bearishPercentage}% of your wins came from bearish setups ({$bearishWins} trades)",
                        'action' => 'Focus on overbought conditions and overvalued assets for your edge'
                    ];
                } elseif (abs($bullishWins - $bearishWins) <= 1 && ($bullishWins + $bearishWins) >= 4) {
                    $recommendations[] = [
                        'type' => 'directional_bias',
                        'title' => 'Direction Agnostic Trader',
                        'description' => "Balanced success in both bullish ({$bullishWins}) and bearish ({$bearishWins}) setups",
                        'action' => 'Your flexibility in market direction is your competitive advantage'
                    ];
                }

                // High confidence pattern recommendations
                $highConfidenceWins = ($directionalBiasPatterns['Bullish (High Confidence)']['count'] ?? 0) +
                    ($directionalBiasPatterns['Bearish (High Confidence)']['count'] ?? 0);
                if ($highConfidenceWins >= 3) {
                    $confidencePercentage = round(($highConfidenceWins / $totalDirectionalWins) * 100, 1);
                    $recommendations[] = [
                        'type' => 'directional_confidence',
                        'title' => 'High Conviction Performer',
                        'description' => "{$confidencePercentage}% of wins came from high-confidence directional calls ({$highConfidenceWins} trades)",
                        'action' => 'Trust your analysis when multiple directional factors align strongly'
                    ];
                }
            }
        }

        return array_slice($recommendations, 0, 4); // Return top 4 recommendations
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
