<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class RegisterController extends Controller
{
    /**
     * Show the registration form
     */
    public function create()
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle registration
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create default user settings
        UserSettings::create([
            'user_id' => $user->id,
            // Default scoring weights - using actual database column names
            'zone_fresh_weight' => 4,
            'zone_original_weight' => 4,
            'zone_flip_weight' => 4,
            'zone_lol_weight' => 4,
            'zone_min_profit_margin_weight' => 4,
            'zone_big_brother_weight' => 4,
            'technical_very_exp_chp_weight' => 12,
            'technical_exp_chp_weight' => 6,
            'technical_direction_impulsive_weight' => 12,
            'technical_direction_correction_weight' => 6,
            'fundamental_valuation_weight' => 10,
            'fundamental_seasonal_weight' => 6,
            'fundamental_cot_index_weight' => 12,
            'fundamental_noncommercial_divergence_weight' => 12,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Welcome to Trade Plan App! Your account has been created successfully.');
    }
}
