<?php

namespace App\Http\Controllers;

use App\Models\AnalysisTracker;
use App\Models\ChecklistWeights;
use App\Models\Instrument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AnalysisTrackerController extends Controller
{
    public function index()
    {
        $trackers = AnalysisTracker::where('user_id', 1) // Replace with Auth::id() when auth is implemented
            ->orderBy('last_updated_at', 'desc')
            ->get();

        $instruments = Instrument::active()->get();

        return Inertia::render('AnalysisTracker/Index', [
            'trackers' => $trackers,
            'instruments' => $instruments,
        ]);
    }

    public function store(Request $request)
    {
        Log::info('STORE method called (this should NOT happen for updates)', [
            'method' => $request->method(),
            'url' => $request->url(),
            'data' => $request->all()
        ]);
        
        $validated = $request->validate([
            'symbol' => 'required|string|max:20',
            'metric_type' => 'required|string',
            'metric_key' => 'nullable|string', 
            'metric_value' => 'required|string',
        ]);

        $tracker = AnalysisTracker::firstOrNew([
            'user_id' => 1, // Replace with Auth::id() when auth is implemented
            'symbol' => $validated['symbol'],
        ]);

        // Initialize tracked_metrics if empty
        if (empty($tracker->tracked_metrics)) {
            $tracker->tracked_metrics = [
                'zone_qualifiers' => [],
                'technicals' => [],
                'fundamentals' => [],
            ];
        }

        $metrics = $tracker->tracked_metrics;

        // Update the specific metric
        if ($validated['metric_type'] === 'zone_qualifiers') {
            // For zone qualifiers, add to array if not exists
            if (!in_array($validated['metric_value'], $metrics['zone_qualifiers'])) {
                $metrics['zone_qualifiers'][] = $validated['metric_value'];
            }
        } else {
            // For technicals and fundamentals, set the key-value pair
            $metrics[$validated['metric_type']][$validated['metric_key']] = $validated['metric_value'];
        }

        $tracker->tracked_metrics = $metrics;
        $tracker->updateCompletion();

        return to_route('analysis-tracker.index', [], 303)->with('success', 'Symbol added to tracker successfully!');
    }

    public function update(Request $request, $id)
    {
        Log::info('Update method called', [
            'method' => $request->method(),
            'url' => $request->url(),
            'id' => $id,
            'request_data' => $request->all()
        ]);

        // Find the tracker explicitly
        $tracker = AnalysisTracker::findOrFail($id);
        
        Log::info('Found tracker', [
            'id' => $tracker->id,
            'symbol' => $tracker->symbol,
            'exists' => $tracker->exists
        ]);

        $validated = $request->validate([
            'tracked_metrics' => 'required|array',
        ]);

        $tracker->tracked_metrics = $validated['tracked_metrics'];
        $tracker->updateCompletion();

        return to_route('analysis-tracker.index', [], 303)->with('success', 'Analysis updated successfully!');
    }

    public function removeMetric(Request $request, $id)
    {
        $tracker = AnalysisTracker::findOrFail($id);
        
        $validated = $request->validate([
            'metric_type' => 'required|string|in:zone_qualifiers,technicals,fundamentals',
            'metric_key' => 'required|string',
        ]);

        $metrics = $tracker->tracked_metrics;

        if ($validated['metric_type'] === 'zone_qualifiers') {
            // Remove from array
            $metrics['zone_qualifiers'] = array_values(
                array_filter($metrics['zone_qualifiers'], function($item) use ($validated) {
                    return $item !== $validated['metric_key'];
                })
            );
        } else {
            // Remove the key from technicals/fundamentals
            unset($metrics[$validated['metric_type']][$validated['metric_key']]);
        }

        $tracker->tracked_metrics = $metrics;
        $tracker->updateCompletion();

        return to_route('analysis-tracker.index', [], 303)->with('success', 'Metric removed successfully!');
    }

    public function destroy(Request $request, $id)
    {
        $tracker = AnalysisTracker::findOrFail($id);
        
        $tracker->delete();
        
        return to_route('analysis-tracker.index', [], 303)->with('success', 'Analysis tracker deleted successfully!');
    }

    public function startChecklist(string $symbol)
    {
        $tracker = AnalysisTracker::where('user_id', 1) // Replace with Auth::id() when auth is implemented
            ->where('symbol', $symbol)
            ->first();

        $instruments = Instrument::active()->get();
        $settings = ChecklistWeights::firstOrCreate(
            ['user_id' => 1], // Replace with Auth::id() when auth is implemented
        );

        return Inertia::render('ChecklistWizard', [
            'settings' => $settings,
            'instruments' => $instruments,
            'prefilledData' => $tracker ? $tracker->tracked_metrics : null,
            'symbol' => $symbol,
        ]);
    }
}
