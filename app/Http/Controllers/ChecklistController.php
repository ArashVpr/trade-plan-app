<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ChecklistController extends Controller
{
    //     public function index()
    // {
    //     $checklists = Checklist::where('user_id', auth()->id())
    //         ->select('id', 'asset', 'score', 'created_at')
    //         ->latest()
    //         ->paginate(10);

    //     return Inertia::render('Checklists/Index', [
    //         'checklists' => $checklists
    //     ]);
    // }
    public function index()
    {
        $checklists = Checklist::where('user_id', '=', 1)->latest()->paginate(10);  // Assuming a static user ID for now; replace with auth()->id() in production
        return Inertia::render('Checklist/Index', ['checklists' => $checklists]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'zone_qualifiers' => 'array',
            'technicals' => 'array',
            'fundamentals' => 'array',
            'score' => 'integer|min:0|max:100',
            'asset' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $checklist = Checklist::create([
            'user_id' => 1, // Assuming a static user ID for now; replace with auth()->id() in production
            'zone_qualifiers' => json_encode($validated['zone_qualifiers']),
            'technicals' => json_encode($validated['technicals']),
            'fundamentals' => json_encode($validated['fundamentals']),
            'score' => $validated['score'],
            'asset' => $validated['asset'],
            'notes' => $validated['notes'],
        ]);

        return Inertia::location(route('checklists.index'));
    }
    public function show(Checklist $checklist)
    {
        // Ensure the checklist belongs to the authenticated user
        if ($checklist->user_id !== 1) { // Replace with auth()->id() in production
            abort(403, 'Unauthorized action.');
        }

        $checklist->zone_qualifiers = json_decode($checklist->zone_qualifiers, true);
        $checklist->technicals = json_decode($checklist->technicals, true);
        $checklist->fundamentals = json_decode($checklist->fundamentals, true);

        return Inertia::render('Checklist/Show', [
            'checklist' => $checklist,
        ]);
    }
    public function edit(Checklist $checklist)
    {
        if ($checklist->user_id !== 1) { // Replace with auth()->id() in production
            abort(403, 'Unauthorized');
        }

        $checklist->zone_qualifiers = json_decode($checklist->zone_qualifiers, true);
        $checklist->technicals = json_decode($checklist->technicals, true);
        $checklist->fundamentals = json_decode($checklist->fundamentals, true);

        return Inertia::render('Checklist/Edit', [
            'checklist' => $checklist
        ]);
    }
    public function update(Request $request, Checklist $checklist)
    {
        Log::info($checklist);
        if ($checklist->user_id !== 1) { // Replace with auth()->id() in production
            abort(403, 'Unauthorized');
        }

        Log::info('Updating checklist', ['checklist_id' => $checklist->id]);
        $validated = $request->validate([
            'zone_qualifiers' => 'array|nullable',
            'technicals' => 'array|required',
            'technicals.location' => 'required|string|in:Very Expensive,Expensive,EQ,Cheap,Very Cheap',
            'technicals.direction' => 'required|string|in:Correction,Impulsion',
            'fundamentals' => 'array|required',
            'fundamentals.valuation' => 'required|string|in:Overvalued,Neutral,Undervalued',
            'fundamentals.seasonalConfluence' => 'required|string|in:Yes,No',
            'fundamentals.nonCommercials' => 'required|string|in:Divergence,No-Divergence',
            'fundamentals.cotIndex' => 'required|string|in:Bullish,Neutral,Bearish',
            'score' => 'required|integer|min:0|max:170',
            'asset' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $checklist->update($validated);

        Log::info('Checklist updated', ['checklist_id' => $checklist->id]);

        return Inertia::location(route('checklists.show', $checklist));
    }

    public function destroy(Checklist $checklist)
    {
        if ($checklist->user_id !== 1) { // Replace with auth()->id() in production
            abort(403, 'Unauthorized');
        }

        $checklist->delete();

        Log::info('Checklist deleted', ['checklist_id' => $checklist->id]);

        return Inertia::location(route('checklists.index'));
    }
}
