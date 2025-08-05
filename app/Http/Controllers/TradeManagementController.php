<?php

namespace App\Http\Controllers;

use App\Models\TradeEntry;
use App\Models\TradeManagementItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TradeManagementController extends Controller
{
    /**
     * Display the trade management dashboard
     */
    public function index()
    {
        $userId = 1; // Replace with auth()->id() when authentication is implemented

        // Get all open and pending trades with their management items
        $openTrades = TradeEntry::with(['checklist', 'managementItems'])
            ->where('user_id', $userId)
            ->whereIn('trade_status', ['pending', 'active'])
            ->orderBy('entry_date', 'desc')
            ->get();

        // Get all management items that need attention (pending, overdue, or due soon)
        $alertItems = TradeManagementItem::with(['tradeEntry.checklist'])
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->where(function ($query) {
                $query->where('due_date', '<=', now()->addDays(3))
                    ->orWhere('priority', 'critical');
            })
            ->orderBy('priority', 'desc')
            ->orderBy('due_date', 'asc')
            ->get();

        // Get predefined management items for selection
        $predefinedItems = TradeManagementItem::getPredefinedItems();

        return Inertia::render('TradeManagement/Index', [
            'openTrades' => $openTrades,
            'alertItems' => $alertItems,
            'predefinedItems' => $predefinedItems,
        ]);
    }

    /**
     * Store a new management item
     */
    public function store(Request $request)
    {
        $userId = 1; // Replace with auth()->id()

        $validated = $request->validate([
            'trade_entry_id' => 'required|exists:trade_entries,id',
            'type' => 'required|in:technical,fundamental,risk,time,custom',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,critical',
            'due_date' => 'nullable|date',
            'metadata' => 'nullable|array',
        ]);

        $validated['user_id'] = $userId;

        TradeManagementItem::create($validated);

        return redirect()->back()->with('success', 'Management item added successfully.');
    }

    /**
     * Update management item status
     */
    public function updateStatus(TradeManagementItem $managementItem, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,ignored,triggered',
            'notes' => 'nullable|string',
        ]);

        $managementItem->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $managementItem->notes,
            'triggered_at' => in_array($validated['status'], ['completed', 'triggered']) ? now() : null,
        ]);

        return redirect()->back()->with('success', 'Management item updated successfully.');
    }

    /**
     * Add predefined management items to a trade
     */
    public function addPredefined(Request $request)
    {
        $userId = 1; // Replace with auth()->id()

        $validated = $request->validate([
            'trade_entry_id' => 'required|exists:trade_entries,id',
            'items' => 'required|array',
            'items.*.type' => 'required|string',
            'items.*.title' => 'required|string',
            'items.*.description' => 'nullable|string',
            'items.*.priority' => 'required|string',
            'items.*.due_date' => 'nullable|date',
        ]);

        foreach ($validated['items'] as $item) {
            TradeManagementItem::create([
                'trade_entry_id' => $validated['trade_entry_id'],
                'user_id' => $userId,
                'type' => $item['type'],
                'title' => $item['title'],
                'description' => $item['description'] ?? null,
                'priority' => $item['priority'],
                'due_date' => $item['due_date'] ?? null,
                'is_predefined' => true,
            ]);
        }

        return redirect()->back()->with('success', 'Predefined management items added successfully.');
    }

    /**
     * Update a management item
     */
    public function update(TradeManagementItem $managementItem, Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,critical',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $managementItem->update($validated);

        return redirect()->back()->with('success', 'Management item updated successfully.');
    }

    /**
     * Delete a management item
     */
    public function destroy(TradeManagementItem $managementItem)
    {
        $managementItem->delete();

        return redirect()->back()->with('success', 'Management item deleted successfully.');
    }
}
