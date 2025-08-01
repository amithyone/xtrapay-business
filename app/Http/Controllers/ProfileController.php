<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\PinUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            
            // Store new photo
            $data['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        // Handle email verification reset
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->fill($data);
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return Redirect::route('profile.edit')->with('status', 'password-updated');
    }

    /**
     * Update the user's PIN.
     */
    public function updatePin(PinUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Verify current PIN
        if ($user->pin !== $request->current_pin) {
            return Redirect::route('profile.edit')
                ->withErrors(['current_pin' => 'Current PIN is incorrect.'])
                ->withInput();
        }

        $user->update([
            'pin' => $request->new_pin
        ]);

        // Also update business profile PIN if it exists
        if ($user->businessProfile) {
            $user->businessProfile->update([
                'pin' => $request->new_pin
            ]);
        }

        return Redirect::route('profile.edit')->with('status', 'pin-updated');
    }

    /**
     * Create a new PIN for the user.
     */
    public function createPin(Request $request): RedirectResponse
    {
        $request->validate([
            'new_pin' => ['required', 'string', 'max:4', 'min:4', 'regex:/^[0-9]+$/'],
            'confirm_pin' => ['required', 'same:new_pin'],
        ], [
            'new_pin.regex' => 'PIN must contain only numbers.',
            'new_pin.min' => 'PIN must be exactly 4 digits.',
            'new_pin.max' => 'PIN must be exactly 4 digits.',
            'confirm_pin.same' => 'PIN confirmation does not match.',
        ]);

        $user = $request->user();
        
        if ($user->pin) {
            return Redirect::route('profile.edit')
                ->withErrors(['new_pin' => 'PIN already exists. Use change PIN instead.'])
                ->withInput();
        }

        $user->update([
            'pin' => $request->new_pin
        ]);

        // Also update business profile PIN if it exists
        if ($user->businessProfile) {
            $user->businessProfile->update([
                'pin' => $request->new_pin
            ]);
        }

        return Redirect::route('profile.edit')->with('status', 'pin-created');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
} 