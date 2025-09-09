<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password form
     */
    public function create()
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    /**
     * Send password reset link
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'exists:users,email'
            ],
        ], [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.exists' => 'No account found with this email address.',
        ]);

        // Send password reset link
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('success', 'We have emailed your password reset link.');
        }

        throw ValidationException::withMessages([
            'email' => 'We are unable to send a password reset link at this time. Please try again later.',
        ]);
    }
}
