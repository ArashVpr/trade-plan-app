<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function create()
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Handle login attempt
     */
    public function store(Request $request)
    {
        // Rate limiting - prevent brute force attacks
        $this->ensureIsNotRateLimited($request);

        $credentials = $request->validate([
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'exists:users,email'
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255'
            ],
        ], [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.exists' => 'No account found with this email address.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Clear rate limiting on successful login
            $this->clearRateLimit($request);

            // Log successful login for security monitoring
            logger('User logged in successfully', [
                'user_id' => Auth::id(),
                'email' => $credentials['email'],
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $redirectTo = $request->session()->pull('url.intended', route('dashboard'));

            return redirect($redirectTo)->with('success', 'Welcome back! You have been successfully logged in.');
        }

        // Hit rate limiter on failed attempt
        $this->hitRateLimit($request);

        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Handle logout
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out.');
    }

    /**
     * Ensure the user is not rate limited
     */
    protected function ensureIsNotRateLimited(Request $request)
    {
        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }
    }

    /**
     * Hit the rate limiter
     */
    protected function hitRateLimit(Request $request)
    {
        RateLimiter::hit($this->throttleKey($request), 300); // 5 minutes
    }

    /**
     * Clear the rate limiter
     */
    protected function clearRateLimit(Request $request)
    {
        RateLimiter::clear($this->throttleKey($request));
    }

    /**
     * Get the rate limiting throttle key
     */
    protected function throttleKey(Request $request)
    {
        return Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());
    }
}
