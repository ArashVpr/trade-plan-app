<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChecklistRequest;
use App\Http\Requests\UpdateChecklistRequest;
use App\Models\Checklist;
use App\Models\ChecklistWeights;
use App\Models\Instrument;
use App\Models\TradeEntry;
use App\Traits\HasTradeStatistics;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ChecklistController extends Controller
{
    use HasTradeStatistics;

    public function index(Request $request): Response
    {
        // Get user's checklist weights FIRST for bias calculation logic
        $settings = ChecklistWeights::firstOrCreate(
            ['user_id' => Auth::id()],
        );

        // Get sorting parameters
        $sortField = $request->get('sortField', 'created_at');
        $sortOrder = $request->get('sortOrder', 'desc');

        // Get search and filter parameters
        $search = $request->get('search', '');
        $biasFilters = $request->get('bias', []);
        $positionFilters = $request->get('position', []);
        $tradeStatusFilters = $request->get('tradeStatus', []);

        // --- PREPARE BIAS CALCULATION SQL (Used in Search & Filters) ---
        // Construct the weighted sum SQL expression based on user settings
        $techVeryExpChp = (int) $settings->technical_very_exp_chp_weight;
        $techExpChp = (int) $settings->technical_exp_chp_weight;
        $fundValuation = (int) $settings->fundamental_valuation_weight;
        $fundSeasonal = (int) $settings->fundamental_seasonal_weight;
        $fundNonComm = (int) $settings->fundamental_noncommercial_divergence_weight;
        $fundCot = (int) $settings->fundamental_cot_index_weight;

        // Calculate maxSum for confidence percentage
        $maxTechWeight = max($techVeryExpChp, $techExpChp);
        $maxSum = (1 * $maxTechWeight) + $fundValuation + $fundSeasonal + $fundNonComm + $fundCot;
        $maxSum = $maxSum > 0 ? $maxSum : 1;

        $weightedSumSql = "
            (
                /* Technical Location */
                (CASE 
                    WHEN JSON_UNQUOTE(JSON_EXTRACT(technicals, '$.location')) IN ('Very Cheap', 'Cheap') THEN 
                        CASE WHEN JSON_UNQUOTE(JSON_EXTRACT(technicals, '$.location')) = 'Very Cheap' THEN {$techVeryExpChp} ELSE {$techExpChp} END
                    WHEN JSON_UNQUOTE(JSON_EXTRACT(technicals, '$.location')) IN ('Very Expensive', 'Expensive') THEN 
                        CASE WHEN JSON_UNQUOTE(JSON_EXTRACT(technicals, '$.location')) = 'Very Expensive' THEN -{$techVeryExpChp} ELSE -{$techExpChp} END
                    ELSE 0 
                END) +
                
                /* Fundamental Valuation */
                (CASE 
                    WHEN JSON_UNQUOTE(JSON_EXTRACT(fundamentals, '$.valuation')) = 'Undervalued' THEN {$fundValuation}
                    WHEN JSON_UNQUOTE(JSON_EXTRACT(fundamentals, '$.valuation')) = 'Overvalued' THEN -{$fundValuation}
                    ELSE 0 
                END) +
                
                /* Fundamental Seasonality */
                (CASE 
                    WHEN JSON_UNQUOTE(JSON_EXTRACT(fundamentals, '$.seasonalConfluence')) = 'Bullish' THEN {$fundSeasonal}
                    WHEN JSON_UNQUOTE(JSON_EXTRACT(fundamentals, '$.seasonalConfluence')) = 'Bearish' THEN -{$fundSeasonal}
                    ELSE 0 
                END) +
                
                /* Fundamental NonCommercials */
                (CASE 
                    WHEN JSON_UNQUOTE(JSON_EXTRACT(fundamentals, '$.nonCommercials')) = 'Bullish Divergence' THEN {$fundNonComm}
                    WHEN JSON_UNQUOTE(JSON_EXTRACT(fundamentals, '$.nonCommercials')) = 'Bearish Divergence' THEN -{$fundNonComm}
                    ELSE 0 
                END) +
                
                /* Fundamental COT Index */
                (CASE 
                    WHEN JSON_UNQUOTE(JSON_EXTRACT(fundamentals, '$.cotIndex')) = 'Bullish' THEN {$fundCot}
                    WHEN JSON_UNQUOTE(JSON_EXTRACT(fundamentals, '$.cotIndex')) = 'Bearish' THEN -{$fundCot}
                    ELSE 0 
                END)
            )
        ";
        $confidenceSql = "ROUND((ABS({$weightedSumSql}) / {$maxSum}) * 100)";
        // -------------------------------------------------------------

        // Build query with sorting
        $query = Checklist::with('tradeEntry')
            ->where('checklists.user_id', Auth::id());

        // Apply search filter
        if (! empty($search)) {
            $query->where(function ($q) use ($search, $weightedSumSql, $confidenceSql) {
                // 1. Symbol Search
                $q->where('checklists.symbol', 'LIKE', "%{$search}%");

                // 2. Score Search (if numeric)
                if (is_numeric($search)) {
                    $q->orWhere('checklists.score', (int) $search);
                }

                // 3. Trade Entry Fields
                $q->orWhereHas('tradeEntry', function ($subQ) use ($search) {
                    $subQ->where('trade_status', 'LIKE', "%{$search}%")
                        ->orWhere('position_type', 'LIKE', "%{$search}%");
                });

                // 4. Handle "Analysis Only" (rows without trade entry)
                if (stripos($search, 'Analysis') !== false || stripos($search, 'Only') !== false) {
                    $q->orWhereDoesntHave('tradeEntry');
                }

                // 5. Bias Keyword Search (using calculated SQL)
                $term = strtolower($search);
                // "Lean Buy" specific
                if (str_contains($term, 'lean buy')) {
                    $q->orWhereRaw("{$weightedSumSql} > 0 AND {$confidenceSql} <= 50 AND {$confidenceSql} > 20");
                }
                // "Lean Sell" specific
                elseif (str_contains($term, 'lean sell')) {
                    $q->orWhereRaw("{$weightedSumSql} < 0 AND {$confidenceSql} <= 50 AND {$confidenceSql} > 20");
                }
                // Generic "Buy" (includes Lean, Strong, Normal)
                elseif (str_contains($term, 'buy')) {
                    $q->orWhereRaw("{$weightedSumSql} > 0");
                }
                // Generic "Sell" (includes Lean, Strong, Normal)
                elseif (str_contains($term, 'sell')) {
                    // Avoid overlapping with "Close" or similar if needed, but "sell" is distinct enough usually
                    $q->orWhereRaw("{$weightedSumSql} < 0");
                }
                // "Neutral"
                elseif (str_contains($term, 'neutral')) {
                    $q->orWhereRaw("{$confidenceSql} <= 20");
                }
            });
        }

        // Apply bias filter using complex weighted logic
        if (! empty($biasFilters)) {
            $query->where(function ($q) use ($biasFilters, $weightedSumSql, $confidenceSql) {
                foreach ($biasFilters as $bias) {
                    switch ($bias) {
                        case 'buy':
                            // Merged Strong Buy logic (>= 51)
                            $q->orWhereRaw("{$weightedSumSql} > 0 AND {$confidenceSql} >= 51");
                            break;
                        case 'lean_buy':
                            $q->orWhereRaw("{$weightedSumSql} > 0 AND {$confidenceSql} <= 50 AND {$confidenceSql} > 20");
                            break;
                        case 'neutral':
                            $q->orWhereRaw("{$confidenceSql} <= 20");
                            break;
                        case 'lean_sell':
                            $q->orWhereRaw("{$weightedSumSql} < 0 AND {$confidenceSql} <= 50 AND {$confidenceSql} > 20");
                            break;
                        case 'sell':
                            // Merged Strong Sell logic (>= 51)
                            $q->orWhereRaw("{$weightedSumSql} < 0 AND {$confidenceSql} >= 51");
                            break;
                    }
                }
            });
        }

        // Apply position filter
        if (! empty($positionFilters)) {
            $query->leftJoin('trade_entries', 'checklists.id', '=', 'trade_entries.checklist_id')
                ->select('checklists.*')
                ->whereIn('trade_entries.position_type', $positionFilters);
        }

        // Apply trade status filter
        if (! empty($tradeStatusFilters)) {
            if (in_array('analysis_only', $tradeStatusFilters)) {
                // For analysis_only, we need rows where trade_entry doesn't exist
                $hasOtherStatuses = count(array_diff($tradeStatusFilters, ['analysis_only'])) > 0;
                if ($hasOtherStatuses) {
                    $otherStatuses = array_diff($tradeStatusFilters, ['analysis_only']);
                    if (empty($query->getQuery()->joins)) {
                        $query->leftJoin('trade_entries', 'checklists.id', '=', 'trade_entries.checklist_id')
                            ->select('checklists.*');
                    }
                    $query->where(function ($q) use ($otherStatuses) {
                        $q->whereIn('trade_entries.trade_status', $otherStatuses)
                            ->orWhereNull('trade_entries.id');
                    });
                } else {
                    // Only analysis_only selected
                    $query->doesntHave('tradeEntry');
                }
            } else {
                if (empty($query->getQuery()->joins)) {
                    $query->leftJoin('trade_entries', 'checklists.id', '=', 'trade_entries.checklist_id')
                        ->select('checklists.*');
                }
                $query->whereIn('trade_entries.trade_status', $tradeStatusFilters);
            }
        }

        // Apply sorting based on field
        switch ($sortField) {
            case 'symbol':
                $query->orderBy('checklists.symbol', $sortOrder);
                break;
            case 'score':
                $query->orderBy('checklists.score', $sortOrder);
                break;
            case 'bias':
                // Sort by a calculated bias score (bullish signals count - bearish signals count)
                // This gives a numerical value we can sort by
                $query->orderByRaw(
                    "CAST(JSON_UNQUOTE(JSON_EXTRACT(checklists.technicals, '$.bullish_count')) AS SIGNED) - 
                     CAST(JSON_UNQUOTE(JSON_EXTRACT(checklists.technicals, '$.bearish_count')) AS SIGNED) + 
                     CAST(JSON_UNQUOTE(JSON_EXTRACT(checklists.fundamentals, '$.bullish_count')) AS SIGNED) - 
                     CAST(JSON_UNQUOTE(JSON_EXTRACT(checklists.fundamentals, '$.bearish_count')) AS SIGNED) {$sortOrder}"
                );
                break;
            case 'created_at':
            case 'date':
                $query->orderBy('checklists.created_at', $sortOrder);
                break;
            case 'entry_date':
                if (empty($query->getQuery()->joins)) {
                    $query->leftJoin('trade_entries', 'checklists.id', '=', 'trade_entries.checklist_id');
                }
                $query->select('checklists.*')
                    ->selectRaw('MAX(trade_entries.entry_date) as te_entry_date')
                    ->groupBy('checklists.id')
                    ->orderBy('te_entry_date', $sortOrder);
                break;
            case 'position_type':
                if (empty($query->getQuery()->joins)) {
                    $query->leftJoin('trade_entries', 'checklists.id', '=', 'trade_entries.checklist_id');
                }
                $query->select('checklists.*')
                    ->selectRaw('MAX(trade_entries.position_type) as te_position_type')
                    ->groupBy('checklists.id')
                    ->orderBy('te_position_type', $sortOrder);
                break;
            case 'trade_status':
                if (empty($query->getQuery()->joins)) {
                    $query->leftJoin('trade_entries', 'checklists.id', '=', 'trade_entries.checklist_id');
                }
                $query->select('checklists.*')
                    ->selectRaw('MAX(trade_entries.trade_status) as te_trade_status')
                    ->groupBy('checklists.id')
                    ->orderBy('te_trade_status', $sortOrder);
                break;
            case 'rrr':
                if (empty($query->getQuery()->joins)) {
                    $query->leftJoin('trade_entries', 'checklists.id', '=', 'trade_entries.checklist_id');
                }
                $query->select('checklists.*')
                    ->selectRaw('MAX(trade_entries.rrr) as te_rrr')
                    ->groupBy('checklists.id')
                    ->orderBy('te_rrr', $sortOrder);
                break;
            default:
                $query->orderBy('checklists.created_at', $sortOrder);
        }

        $checklists = $query->paginate(10);

        // Calculate statistics using trait
        $stats = $this->getUserTradeStatistics();

        $instruments = Instrument::active()->get();

        // Get user's checklist weights for directional bias calculation
        // $settings = ChecklistWeights::firstOrCreate(
        //    ['user_id' => Auth::id()],
        // );

        return Inertia::render('Checklist/Index', [
            'checklists' => $checklists,
            'instruments' => $instruments,
            'settings' => $settings,
            'statistics' => [
                'total' => $stats['total_checklists'],
                'analysisOnly' => $stats['analysis_only'],
                'pending' => $stats['pending'],
                'active' => $stats['active'],
                'wins' => $stats['wins'],
                'losses' => $stats['losses'],
                'breakeven' => $stats['breakeven'],
                'cancelled' => $stats['cancelled'],
            ],
        ]);
    }

    public function store(StoreChecklistRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Wrap in transaction to ensure data consistency
        return DB::transaction(function () use ($validated) {
            // Persist checklist and capture its ID
            $checklist = Checklist::create([
                'user_id' => Auth::id(),
                'zone_qualifiers' => $validated['zone_qualifiers'],
                'technicals' => $validated['technicals'],
                'fundamentals' => $validated['fundamentals'],
                'exclude_fundamentals' => $validated['exclude_fundamentals'] ?? false,
                'score' => $validated['score'],
                'symbol' => $validated['symbol'],
            ]);

            // Only create TradeEntry if ALL required order fields are present
            // Required fields: entry_date, position_type, entry_price, stop_price, target_price
            $hasAllRequiredFields =
                ! empty($validated['entry_date']) &&
                ! empty($validated['position_type']) &&
                isset($validated['entry_price']) &&
                isset($validated['stop_price']) &&
                isset($validated['target_price']);

            if ($hasAllRequiredFields) {
                $tradeEntryData = [
                    'user_id' => Auth::id(),
                    'checklist_id' => $checklist->id,
                    'entry_date' => $validated['entry_date'],
                    'position_type' => $validated['position_type'] ?? null,
                    'entry_price' => $validated['entry_price'] ?? null,
                    'stop_price' => $validated['stop_price'] ?? null,
                    'target_price' => $validated['target_price'] ?? null,
                    'rrr' => $validated['rrr'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ];

                // Handle multiple screenshot uploads - check both array and bracket notation
                $screenshotPaths = [];
                if ($validated['screenshots'] ?? null) {
                    // Screenshots passed through validation
                    foreach ($validated['screenshots'] as $screenshot) {
                        $path = $screenshot->store('trade-screenshots', 'public');
                        $screenshotPaths[] = $path;
                    }
                }
                $tradeEntryData['screenshot_paths'] = $screenshotPaths;

                // Only set trade_status if there are actual order entry fields
                // If only screenshots/notes, leave trade_status null (will be treated as "Analysis Only")
                $hasActualOrderDetails = ($validated['entry_date'] ?? null) ||
                    ($validated['position_type'] ?? null) ||
                    ($validated['entry_price'] ?? null) ||
                    ($validated['stop_price'] ?? null) ||
                    ($validated['target_price'] ?? null);

                if ($hasActualOrderDetails) {
                    // Use provided trade_status or default to 'pending' if actual order details exist
                    $tradeEntryData['trade_status'] = $validated['trade_status'] ?? 'pending';
                }
                // If no actual order details, don't set trade_status (keeps it NULL)

                TradeEntry::create($tradeEntryData);
            }

            return to_route('checklists.index')->with('success', 'Checklist created successfully!');
        });
    }

    /**
     * Display the specified checklist.
     */
    public function show(Checklist $checklist): Response
    {
        // Fetch the entry tied to this specific checklist
        $tradeEntry = TradeEntry::where('checklist_id', $checklist->id)->first();
        $instruments = Instrument::active()->get();

        // Get user's checklist weights for directional bias calculation
        $settings = ChecklistWeights::firstOrCreate(
            ['user_id' => Auth::id()],
        );

        return Inertia::render('Checklist/Show', [
            'checklist' => $checklist,
            'tradeEntry' => $tradeEntry,
            'instruments' => $instruments,
            'settings' => $settings,
        ]);
    }

    /**
     * Show the form for editing the specified checklist.
     */
    public function edit(Checklist $checklist): Response
    {

        $settings = ChecklistWeights::firstOrCreate(
            ['user_id' => Auth::id()],
        );
        // Fetch the entry tied to this specific checklist
        $tradeEntry = TradeEntry::where('checklist_id', $checklist->id)->first();
        $instruments = Instrument::active()->get();

        return Inertia::render('Checklist/Edit', [
            'checklist' => $checklist,
            'settings' => $settings,
            'tradeEntry' => $tradeEntry,
            'instruments' => $instruments,
        ]);
    }

    /**
     * Update the specified checklist in storage.
     */
    public function update(UpdateChecklistRequest $request, Checklist $checklist): RedirectResponse
    {
        try {
            if ($checklist->user_id !== Auth::id()) {
                abort(403, 'Unauthorized');
            }

            // Parse JSON strings from FormData if they exist
            $technicals = $request->input('technicals');
            $fundamentals = $request->input('fundamentals');
            $zoneQualifiers = $request->input('zone_qualifiers');
            $existingScreenshots = $request->input('existing_screenshots');

            if (is_string($technicals)) {
                $technicals = json_decode($technicals, true);
            }
            if (is_string($fundamentals)) {
                $fundamentals = json_decode($fundamentals, true);
            }
            if (is_string($zoneQualifiers)) {
                $zoneQualifiers = json_decode($zoneQualifiers, true);
            }
            if (is_string($existingScreenshots)) {
                $existingScreenshots = json_decode($existingScreenshots, true);
            }

            // Update both Checklist and its TradeEntry atomically
            $validated = $request->validated();

            DB::transaction(function () use ($checklist, $validated, $technicals, $fundamentals, $zoneQualifiers, $existingScreenshots) {
                // Update checklist fields (excluding symbol)
                $checklistData = [];

                if ($zoneQualifiers) {
                    $checklistData['zone_qualifiers'] = $zoneQualifiers;
                }
                if ($technicals) {
                    $checklistData['technicals'] = $technicals;
                }
                if ($fundamentals) {
                    $checklistData['fundamentals'] = $fundamentals;
                }
                if (isset($validated['exclude_fundamentals'])) {
                    $checklistData['exclude_fundamentals'] = $validated['exclude_fundamentals'];
                }
                if (isset($validated['score'])) {
                    $checklistData['score'] = $validated['score'];
                }

                if (! empty($checklistData)) {
                    $checklist->update($checklistData);
                }

                // Create/update trade entry if ANY order field has a value
                $hasTradeDetails = ($validated['entry_date'] ?? null) ||
                    ($validated['position_type'] ?? null) ||
                    ($validated['entry_price'] ?? null) ||
                    ($validated['stop_price'] ?? null) ||
                    ($validated['target_price'] ?? null) ||
                    ($validated['notes'] ?? null) ||
                    ($validated['screenshots'] ?? null) ||
                    ($existingScreenshots ?? null);

                if ($hasTradeDetails) {
                    // Prepare trade entry data
                    $tradeData = Arr::only($validated, [
                        'entry_date',
                        'position_type',
                        'entry_price',
                        'stop_price',
                        'target_price',
                        'rrr',
                        'notes',
                    ]);

                    // Handle multiple screenshot uploads
                    $screenshotPaths = $existingScreenshots ?? [];
                    if (! is_array($screenshotPaths)) {
                        $screenshotPaths = [];
                    }

                    // Check both validated screenshots and request files
                    if (($validated['screenshots'] ?? null) && is_array($validated['screenshots'])) {
                        foreach ($validated['screenshots'] as $screenshot) {
                            if (count($screenshotPaths) < 5) {
                                try {
                                    $path = $screenshot->store('trade-screenshots', 'public');
                                    if ($path) {
                                        $screenshotPaths[] = $path;
                                    } else {
                                        Log::warning('Failed to store screenshot for checklist ' . $checklist->id);
                                    }
                                } catch (\Exception $e) {
                                    Log::error('Screenshot storage error: ' . $e->getMessage(), [
                                        'checklist_id' => $checklist->id,
                                        'error' => $e,
                                    ]);

                                    // Continue with other screenshots
                                    continue;
                                }
                            }
                        }
                    }

                    $tradeData['screenshot_paths'] = $screenshotPaths;

                    // Only include trade_status if it has a value, otherwise let DB default to 'pending'
                    if (! empty($validated['trade_status'])) {
                        $tradeData['trade_status'] = $validated['trade_status'];
                    }

                    // Add user_id for new record creation
                    $tradeData['user_id'] = Auth::id();

                    // Update or create the related trade entry
                    try {
                        $checklist->tradeEntry()->updateOrCreate(
                            ['checklist_id' => $checklist->id],
                            $tradeData
                        );
                    } catch (\Exception $e) {
                        Log::error('Trade entry update error: ' . $e->getMessage(), [
                            'checklist_id' => $checklist->id,
                            'tradeData' => $tradeData,
                            'error' => $e,
                        ]);
                        throw $e;
                    }
                }
            });

            return to_route('checklists.show', $checklist->id, 303)->with('success', 'Checklist updated successfully!');
        } catch (\Exception $e) {
            Log::error('Checklist update error: ' . $e->getMessage(), [
                'exception' => $e,
                'checklist_id' => $checklist->id,
                'user_id' => Auth::id(),
            ]);
            throw $e;
        }
    }

    /**
     * Remove the specified checklist from storage.
     */
    public function destroy(Checklist $checklist): RedirectResponse
    {
        $checklist->delete();

        return to_route('checklists.index');
    }

    public function checklistWeights(Request $request): Response
    {
        $settings = ChecklistWeights::firstOrCreate(
            ['user_id' => Auth::id()],
        );
        $instruments = Instrument::active()->get();

        return Inertia::render('ChecklistWizard', [
            'settings' => $settings->toArray(),
            'instruments' => $instruments,
            'prefilledData' => $request->query('prefilled'),
            'symbol' => $request->query('symbol'),
        ]);
    }
}
