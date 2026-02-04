<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateChecklistWeightsRequest;
use App\Models\ChecklistWeights;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ChecklistWeightsController extends Controller
{
    /**
     * Display the checklist weights configuration.
     */
    public function index(): Response
    {
        // Retrieve default attribute values from the model
        $defaults = (new ChecklistWeights)->getAttributes();
        // Create or fetch checklist weights, using defaults on create
        $weights = ChecklistWeights::firstOrCreate(
            ['user_id' => Auth::id()],
            $defaults
        );

        // Pass both persisted weights and defaults to the view
        return Inertia::render('ChecklistWeights', [
            'weights' => $weights,
            'defaults' => $defaults,
        ]);
    }

    /**
     * Update the checklist weights in storage.
     */
    public function update(UpdateChecklistWeightsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        ChecklistWeights::updateOrCreate(
            ['user_id' => Auth::id()],
            $validated
        );

        return back()->with('success', 'Checklist weights updated successfully!');
    }
}
