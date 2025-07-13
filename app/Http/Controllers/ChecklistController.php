<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ChecklistController extends Controller
{
    public function index()
    {
        $checklists = Checklist::where('user_id', '=', 1)->get();  // Assuming a static user ID for now; replace with auth()->id() in production
        // dd($checklists);
        return Inertia::render('Index', ['checklists' => $checklists]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'zone_qualifiers' => 'array',
            'technicals' => 'array',
            'fundamentals' => 'array',
            'score' => 'integer|min:0|max:170',
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

        return redirect()->route('checklists.index')->with('success', 'Checklist saved successfully.');
    }
}
