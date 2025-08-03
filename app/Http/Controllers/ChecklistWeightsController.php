<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ChecklistWeights;
use Inertia\Inertia;

class ChecklistWeightsController extends Controller
{
    public function index()
    {
        // Retrieve default attribute values from the model
        $defaults = (new ChecklistWeights)->getAttributes();
        // Create or fetch checklist weights, using defaults on create
        $weights = ChecklistWeights::firstOrCreate(
            ['user_id' => 1], // Replace with auth()->id()
            $defaults
        );

        // Pass both persisted weights and defaults to the view
        return Inertia::render('ChecklistWeights', [
            'weights' => $weights,
            'defaults' => $defaults,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'zone_fresh_weight' => 'required|integer|min:0|max:100',
            'zone_original_weight' => 'required|integer|min:0|max:100',
            'zone_flip_weight' => 'required|integer|min:0|max:100',
            'zone_lol_weight' => 'required|integer|min:0|max:100',
            'zone_min_profit_margin_weight' => 'required|integer|min:0|max:100',
            'zone_big_brother_weight' => 'required|integer|min:0|max:100',
            'technical_very_exp_chp_weight' => 'required|integer|min:0|max:100',
            'technical_exp_chp_weight' => 'required|integer|min:0|max:100',
            'technical_direction_impulsive_weight' => 'required|integer|min:0|max:100',
            'technical_direction_correction_weight' => 'required|integer|min:0|max:100',
            'fundamental_valuation_weight' => 'required|integer|min:0|max:100',
            'fundamental_seasonal_weight' => 'required|integer|min:0|max:100',
            'fundamental_cot_index_weight' => 'required|integer|min:0|max:100',
            'fundamental_noncommercial_divergence_weight' => 'required|integer|min:0|max:100',
        ]);

        $weights = ChecklistWeights::updateOrCreate(
            ['user_id' => 1], // Replace with auth()->id()
            $validated
        );

        return back()->with('success', 'Checklist weights updated successfully!');
    }
}
