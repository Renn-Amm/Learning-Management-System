<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updateName(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $request->user()->update($validated);

        return Redirect::route('profile.edit')->with('success', 'Name updated successfully.');
    }

    public function updateProfileImage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();

        // Delete old profile image if exists
        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }

        // Store new profile image
        $path = $request->file('profile_image')->store('profile-images', 'public');
        
        $user->update(['profile_image' => $path]);

        return Redirect::route('profile.edit')->with('success', 'Profile image updated successfully.');
    }

    public function deleteProfileImage(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
            $user->update(['profile_image' => null]);
        }

        return Redirect::route('profile.edit')->with('success', 'Profile image deleted successfully.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Delete profile image if exists
        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }

        // If teacher: Delete all courses and their related data
        if ($user->isTeacher()) {
            foreach ($user->courses as $course) {
                // Delete course thumbnail
                if ($course->thumbnail) {
                    Storage::disk('public')->delete($course->thumbnail);
                }
                
                // Delete lessons and their attachments
                foreach ($course->lessons as $lesson) {
                    if ($lesson->attachment) {
                        Storage::disk('private')->delete($lesson->attachment);
                    }
                    $lesson->delete();
                }
                
                // Delete course (this will cascade delete enrollments due to FK)
                $course->delete();
            }
        }

        // If student: Delete all enrollments (handled by FK cascade)
        // Delete all sent and received messages (handled by FK cascade)
        // Delete categories created by user (handled by FK cascade)

        Auth::logout();

        // Delete user account
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('success', 'Your account has been deleted.');
    }
}
