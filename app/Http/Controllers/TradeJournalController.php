<?php

namespace App\Http\Controllers;

use App\Models\TradeJournal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TradeJournalController extends Controller
{
    // List journals with filtering
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = TradeJournal::where('user_id', $userId);

        // Filters
        if ($request->instrument) {
            $query->where('instrument', 'like', "%{$request->instrument}%");
        }
        if ($request->outcome) {
            $query->where('outcome', $request->outcome);
        }
        if ($request->setup_grade) {
            $query->where('setup_grade', $request->setup_grade);
        }
        if ($request->date_from) {
            $query->whereDate('entry_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('entry_date', '<=', $request->date_to);
        }

        $journals = $query->orderBy('entry_date', 'desc')->paginate(10)->withQueryString();

        return Inertia::render('TradeJournal/Index', [
            'journals' => $journals,
            'filters' => $request->only(['instrument', 'outcome', 'setup_grade', 'date_from', 'date_to']),
        ]);
    }

    // Show create form
    public function create()
    {
        return Inertia::render('TradeJournal/Create');
    }

    // Store a new journal entry
    public function store(Request $request)
    {
        $data = $request->validate([
            'instrument' => 'required|string|max:255',
            'entry_date' => 'required|date',
            'entry_price' => 'required|numeric',
            'stop_price' => 'required|numeric',
            'target_price' => 'required|numeric',
            'notes' => 'nullable|string',
            'setup_grade' => 'nullable|integer|min:0|max:5',
            'screenshot' => 'nullable|image|max:2048',
            'outcome' => 'nullable|in:win,loss,breakeven',
        ]);
        $data['user_id'] = Auth::id();
        // Handle upload
        if ($request->hasFile('screenshot')) {
            $path = $request->file('screenshot')->store('screenshots', 'public');
            $data['screenshot_path'] = $path;
        }
        TradeJournal::create($data);

        return to_route('trade-journals.index')->with('success', 'Journal entry created.');
    }

    // Optionally show detail
    public function show(TradeJournal $tradeJournal)
    {
        // Ensure the journal belongs to the authenticated user
        if ($tradeJournal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        return Inertia::render('TradeJournal/Show', ['journal' => $tradeJournal]);
    }
}
