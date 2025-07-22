<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\UserSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Models\TradeEntry;


class ChecklistController extends Controller
{
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
            // Order entry
            'entry_date' => 'required|date',
            'position_type' => 'required|in:Long,Short',
            'entry_price' => 'required|numeric',
            'stop_price' => 'required|numeric',
            'target_price' => 'required|numeric',
            'outcome' => 'required|in:win,loss,breakeven',
            'notes' => 'nullable|string',
        ]);

        // Persist checklist
        Checklist::create([
            'user_id' => 1, // replace with auth()->id() in production
            'zone_qualifiers' => $validated['zone_qualifiers'],
            'technicals' => $validated['technicals'],
            'fundamentals' => $validated['fundamentals'],
            'score' => $validated['score'],
            'asset' => $validated['asset'],
        ]);
        // Persist corresponding trade entry
        TradeEntry::create([
            'user_id' => 1, // replace with auth()->id() in production
            'instrument' => $validated['asset'],
            'entry_date' => $validated['entry_date'],
            'position_type' => $validated['position_type'],
            'entry_price' => $validated['entry_price'],
            'stop_price' => $validated['stop_price'],
            'target_price' => $validated['target_price'],
            'outcome' => $validated['outcome'],
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

        return Inertia::render('Checklist/Show', [
            'checklist' => $checklist,
        ]);
    }
    public function edit(Checklist $checklist)
    {
        if ($checklist->user_id !== 1) { // Replace with auth()->id() in production
            abort(403, 'Unauthorized');
        }
        $settings = UserSettings::firstOrCreate(
            ['user_id' => 1], // Replace with Auth::id() in production
        );

        return Inertia::render('Checklist/Edit', [
            'checklist' => $checklist,
            'settings' => $settings,
        ]);
    }
    public function update(Request $request, Checklist $checklist)
    {
        if ($checklist->user_id !== 1) { // Replace with auth()->id() in production
            abort(403, 'Unauthorized');
        }

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

        return Inertia::location(route('checklists.show', $checklist));
    }

    public function destroy(Checklist $checklist)
    {
        if ($checklist->user_id !== 1) { // Replace with auth()->id() in production
            abort(403, 'Unauthorized');
        }

        $checklist->delete();

        return Inertia::location(route('checklists.index'));
    }

    public function checklistWeights()
    {
        $settings = UserSettings::firstOrCreate(
            ['user_id' => 1], // Replace with Auth::id() in production
        );
        return Inertia::render('ChecklistWizard', [
            'settings' => $settings,
        ]);
    }
}
