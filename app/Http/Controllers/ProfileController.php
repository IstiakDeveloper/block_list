<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Str;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        try {
            $user = $request->user();

            if ($request->hasFile('photo')) {
                Log::info('Photo file received', ['file' => $request->file('photo')]);

                // Delete the old photo if it exists
                if ($user->photo) {
                    Storage::disk('public')->delete($user->photo);
                }

                $file = $request->file('photo');
                $extension = $file->getClientOriginalExtension();
                $filename = Str::uuid() . '.' . $extension;

                // Store the file and get the path
                $path = $file->storeAs('photos', $filename, 'public');
                Log::info('New photo stored', ['path' => $path]);

                // Update the user's photo field with the new path
                $user->photo = $path;
            }

            // Update other fields
            $user->fill($request->safe()->only(['name', 'email']));

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            Log::info('User updated', ['user' => $user->toArray()]);

            return Redirect::route('profile.edit')->with('status', 'Profile updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating profile', ['error' => $e->getMessage()]);
            return Redirect::route('profile.edit')->with('error', 'An error occurred while updating your profile.');
        }
    }



    public function index()
    {
        // Get the authenticated user
        $user = auth()->user();

        // Return the view with the user data
        return Inertia::render('Component', [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'photo' => $user->profile_photo_path, // Adjust as necessary
            ],
        ]);
    }


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
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
