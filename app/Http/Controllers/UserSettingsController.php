<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSettings;
use Inertia\Inertia;

class UserSettingsController extends Controller
{
    public function index()
    {
        $settings = UserSettings::firstOrCreate(
            ['user_id' => 1], // Replace with Auth::id() in production
            [
                'zone_fresh_weight' => 10, 
                'zone_original_weight' => 10,
                'zone_flip_weight' => 10,
                'zone_lol_weight' => 10,
                'zone_min_profit_margin_weight' => 10,
                'zone_big_brother_weight' => 10,
                'technical_very_exp_chp_weight' => 10,
                'technical_exp_chp_weight' => 10,
                'technical_direction_impulsive_weight' => 10,
                'technical_direction_correction_weight' => 10,
                'fundamental_valuation_weight' => 10,
                'fundamental_seasonal_weight' => 10,
                'fundamental_cot_index_weight' => 10,
                'fundamental_noncommercial_divergence_weight' => 10,
            ]
        );

        return Inertia::render('UserSettings', ['settings' => $settings]);
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

        $totalWeight = array_sum($validated);
        if ($totalWeight !== 100) {
            return back()->withErrors(['error' => 'The total weight must equal 100.']);
        }

        $settings = UserSettings::updateOrCreate(
            ['user_id' => 1], // Replace with Auth::id() in production
            $validated
        );

        return to_route('checklists.index')->with('success', 'Settings updated successfully!');
    }
}
