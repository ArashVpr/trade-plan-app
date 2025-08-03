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
        // Retrieve default attribute values from the model
        $defaults = (new UserSettings)->getAttributes();
        // Create or fetch user settings, using defaults on create
        $settings = UserSettings::firstOrCreate(
            ['user_id' => Auth::id()],
            $defaults
        );

        // Pass both persisted settings and defaults to the view
        return Inertia::render('UserSettings', [
            'settings' => $settings,
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

        $totalWeight = array_sum($validated);

        $settings = UserSettings::updateOrCreate(
            ['user_id' => Auth::id()],
            $validated
        );

        return redirect('/')->with('success', 'Settings updated successfully!');
    }
}
