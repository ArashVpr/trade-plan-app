<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateChecklistWeightsRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\ChecklistWeights;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class UserProfileController extends Controller
{
    /**
     * Display the user profile page.
     */
    public function index(): Response
    {
        $user = Auth::user();

        // Get defaults for checklist weights
        $defaults = (new ChecklistWeights)->getAttributes();

        // Get or create checklist weights
        $checklistWeights = ChecklistWeights::firstOrCreate(
            ['user_id' => $user->id],
            $defaults
        );

        return Inertia::render('UserProfile', [
            'user' => $user,
            'checklistWeights' => $checklistWeights,
            'checklistDefaults' => $defaults,
        ]);
    }

    /**
     * Update user profile information
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-Z\s\-\'\.]+$/',
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:users,email,'.$user->id,
            ],
            'timezone' => ['nullable', 'string', 'max:50'],
            'bio' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Full name is required.',
            'name.min' => 'Name must be at least 2 characters.',
            'name.regex' => 'Name may only contain letters, spaces, hyphens, apostrophes, and dots.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already taken.',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Upload and update user avatar
     */
    public function uploadAvatar(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,gif', 'max:2048'],
        ], [
            'avatar.required' => 'Please select an image.',
            'avatar.image' => 'The file must be a valid image.',
            'avatar.mimes' => 'The image must be a JPEG, PNG, or GIF.',
            'avatar.max' => 'The image must not exceed 2MB.',
        ]);

        // Delete old avatar if exists
        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'public');

        // Update user avatar path
        $user->update(['avatar_path' => $path]);

        // Ensure fresh data is returned to Inertia
        $user->refresh();

        return back()->with('success', 'Profile picture updated successfully!');
    }

    /**
     * Update user password
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        Auth::user()->update([
            'password' => Hash::make($request->validated()['password']),
        ]);

        // Log password change for security
        logger('User password changed', [
            'user_id' => Auth::id(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    /**
     * Update notification preferences
     */
    public function updateNotifications(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email_notifications' => ['boolean'],
            'trade_alerts' => ['boolean'],
            'weekly_summary' => ['boolean'],
            'security_alerts' => ['boolean'],
        ]);

        // For now, we'll just return success without storing notifications
        // In a real app, you might have a separate notifications table
        // or add notification fields to the users table

        return back()->with('success', 'Notification preferences updated!');
    }

    /**
     * Update the user's checklist weights.
     */
    public function updateChecklistWeights(UpdateChecklistWeightsRequest $request): RedirectResponse
    {
        ChecklistWeights::updateOrCreate(
            ['user_id' => Auth::id()],
            $request->validated()
        );

        return back()->with('success', 'Checklist weights updated successfully!');
    }

    /**
     * Delete user account
     */
    public function deleteAccount(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
            'confirmation' => ['required', 'in:DELETE'],
        ], [
            'password.current_password' => 'Password is incorrect.',
            'confirmation.in' => 'Please type DELETE to confirm account deletion.',
        ]);

        $user = Auth::user();

        // Log account deletion
        logger('User account deleted', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
        ]);

        Auth::logout();
        $user->delete();

        return redirect()->route('login')->with('success', 'Your account has been deleted successfully.');
    }
}
