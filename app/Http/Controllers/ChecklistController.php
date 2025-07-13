<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChecklistController extends Controller
{
    public function index()
    {
        $checklists = Checklist::where('user_id', auth()->id())->latest()->get();
        return Inertia::render('Checklists/Index', ['checklists' => $checklists]);
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
            'user_id' => auth()->id(),
            'zone_qualifiers' => $validated['zone_qualifiers'],
            'technicals' => $validated['technicals'],
            'fundamentals' => $validated['fundamentals'],
            'score' => $validated['score'],
            'asset' => $validated['asset'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('checklists.index')->with('success', 'Checklist saved successfully.');
    }
}
