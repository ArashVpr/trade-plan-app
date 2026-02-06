<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ResetPasswordController extends Controller
{
    /**
     * Show the reset password form
     */
    public function create(Request $request)
    {
        return Inertia::render('Auth/ResetPassword', [
            'email' => $request->email,
            'token' => $request->route('token'),
        ]);
    }

    /**
     * Handle password reset
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'exists:users,email',
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Rules\Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers(),
            ],
        ], [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.exists' => 'No account found with this email address.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.letters' => 'Password must contain at least one letter.',
            'password.mixed' => 'Password must contain both uppercase and lowercase letters.',
            'password.numbers' => 'Password must contain at least one number.',
            'password_confirmation.required' => 'Password confirmation is required.',
            'password_confirmation.same' => 'Password confirmation does not match.',
        ]);

        // Reset the password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            // Don't auto-login, just redirect to login with success message
            return redirect()->route('login')->with('success', 'Password reset successful! Please login with your new password.');
        }

        throw ValidationException::withMessages([
            'email' => 'This password reset token is invalid or has expired.',
        ]);
    }
}
