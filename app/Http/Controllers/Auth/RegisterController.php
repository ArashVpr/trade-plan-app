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
use Illuminate\Validation\ValidationException;
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
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-Z\s\-\'\.]+$/', // Only letters, spaces, hyphens, apostrophes, dots
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:users,email',
                'not_regex:/\+.*@/', // Prevent email aliases with +
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
            'password_confirmation' => [
                'required',
                'string',
                'same:password',
            ],
        ], [
            'name.required' => 'Full name is required.',
            'name.min' => 'Name must be at least 2 characters.',
            'name.regex' => 'Name may only contain letters, spaces, hyphens, apostrophes, and dots.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'An account with this email already exists.',
            'email.not_regex' => 'Email aliases with + are not allowed.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.letters' => 'Password must contain at least one letter.',
            'password.mixed' => 'Password must contain both uppercase and lowercase letters.',
            'password.numbers' => 'Password must contain at least one number.',
            'password.symbols' => 'Password must contain at least one symbol.',
            'password.uncompromised' => 'This password has been compromised in a data breach. Please choose a different password.',
            'password_confirmation.required' => 'Password confirmation is required.',
            'password_confirmation.same' => 'Password confirmation does not match.',
        ]);

        // Additional security checks
        $email = strtolower(trim($request->email));
        $name = trim($request->name);

        // Check for disposable email domains (basic check)
        $disposableDomains = ['tempmail.org', '10minutemail.com', 'guerrillamail.com', 'mailinator.com'];
        $emailDomain = substr(strrchr($email, '@'), 1);

        if (in_array($emailDomain, $disposableDomains)) {
            throw ValidationException::withMessages([
                'email' => 'Disposable email addresses are not allowed.',
            ]);
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(), // Auto-verify for now, implement email verification later
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

        // Log successful registration for security monitoring
        logger('User registered successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Welcome to Trade Plan App! Your account has been created successfully and you are now logged in.');
    }
}
