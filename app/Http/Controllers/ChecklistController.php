<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\UserSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Models\TradeEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;


class ChecklistController extends Controller
{
    public function index()
    {
        $checklists = Checklist::with('tradeEntry')
            ->latest()
            ->paginate(10);  // Assuming a static user ID for now; replace with auth()->id() in production
        return Inertia::render('Checklist/Index', ['checklists' => $checklists]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'zone_qualifiers' => 'array',
            'technicals' => 'array',
            'fundamentals' => 'array',
            'score' => 'integer|min:0|max:100',
            'symbol' => 'nullable|string|max:255',
            // Order entry - now optional
            'entry_date' => 'nullable|date',
            'position_type' => 'nullable|in:Long,Short',
            'entry_price' => 'nullable|numeric',
            'stop_price' => 'nullable|numeric',
            'target_price' => 'nullable|numeric',
            'trade_status' => 'nullable|in:pending,active,win,loss,breakeven,cancelled',
            'rrr' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        // Persist checklist and capture its ID
        $checklist = Checklist::create([
            'user_id' => 1, // replace with auth()->id() in production
            'zone_qualifiers' => $validated['zone_qualifiers'],
            'technicals' => $validated['technicals'],
            'fundamentals' => $validated['fundamentals'],
            'score' => $validated['score'],
            'symbol' => $validated['symbol'],
        ]);

        // Only create trade entry if order details are provided
        if ($validated['entry_date'] && $validated['position_type'] && $validated['entry_price'] && $validated['stop_price'] && $validated['target_price']) {
            TradeEntry::create([
                'user_id' => 1, // replace with auth()->id() in production
                'checklist_id' => $checklist->id,
                'entry_date' => $validated['entry_date'],
                'position_type' => $validated['position_type'],
                'entry_price' => $validated['entry_price'],
                'stop_price' => $validated['stop_price'],
                'target_price' => $validated['target_price'],
                'trade_status' => $validated['trade_status'],
                'rrr' => $validated['rrr'],
                'notes' => $validated['notes'],
            ]);
        }

        return to_route('checklists.index')->with('success', 'Checklist created successfully!');


    }
    public function show(Checklist $checklist)
    {
        // Fetch the entry tied to this specific checklist
        $tradeEntry = TradeEntry::where('checklist_id', $checklist->id)->first();

        return Inertia::render('Checklist/Show', [
            'checklist' => $checklist,
            'tradeEntry' => $tradeEntry,
        ]);
    }
    public function edit(Checklist $checklist)
    {

        $settings = UserSettings::firstOrCreate(
            ['user_id' => 1], // Replace with Auth::id() in production
        );
        // Fetch the entry tied to this specific checklist
        $tradeEntry = TradeEntry::where('checklist_id', $checklist->id)->first();

        return Inertia::render('Checklist/Edit', [
            'checklist' => $checklist,
            'settings' => $settings,
            'tradeEntry' => $tradeEntry,
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
            'symbol' => 'nullable|string|max:255',
            // Order entry - now optional for updates too
            'entry_date' => 'nullable|date',
            'position_type' => 'nullable|in:Long,Short',
            'entry_price' => 'nullable|numeric',
            'stop_price' => 'nullable|numeric',
            'target_price' => 'nullable|numeric',
            'trade_status' => 'nullable|in:pending,active,win,loss,breakeven,cancelled',
            'rrr' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        // Update both Checklist and its TradeEntry atomically
        DB::transaction(function () use ($checklist, $validated) {
            // Update checklist fields
            $checklist->update(Arr::only($validated, [
                'zone_qualifiers',
                'technicals',
                'fundamentals',
                'score',
                'symbol',
            ]));

            // Only update/create trade entry if order details are provided
            if ($validated['entry_date'] && $validated['position_type'] && $validated['entry_price'] && $validated['stop_price'] && $validated['target_price']) {
                // Prepare trade entry data
                $tradeData = Arr::only($validated, [
                    'entry_date',
                    'position_type',
                    'entry_price',
                    'stop_price',
                    'target_price',
                    'rrr',
                    'trade_status',
                    'notes'
                ]);

                // Add user_id for new record creation
                $tradeData['user_id'] = 1; // Replace with Auth::id() in production

                // Update or create the related trade entry
                $checklist->tradeEntry()->updateOrCreate(
                    ['checklist_id' => $checklist->id,],
                    $tradeData
                );
            }
        });

        return to_route('checklists.show', $checklist->id, 303)->with('success', 'Checklist updated successfully!');

    }

    public function destroy(Checklist $checklist)
    {
        $checklist->delete();

        return to_route('checklists.index');
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
