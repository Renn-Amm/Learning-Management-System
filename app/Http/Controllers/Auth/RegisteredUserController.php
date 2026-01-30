<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // Enhanced email validation with DNS check for real emails
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 
                'string', 
                'lowercase', 
                'email:rfc,dns',  // Validates email format AND checks DNS records
                'max:255', 
                'unique:users,email'  // Check if email already exists in database
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:student,teacher'],
        ], [
            'email.unique' => 'This email address is already registered. Please login instead.',
            'email.email' => 'Please enter a valid email address from a real domain.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Send email verification notification
        event(new Registered($user));

        Auth::login($user);

        // Redirect to email verification notice if verification is required
        return redirect(route('dashboard', absolute: false))
            ->with('status', 'Please verify your email address. Check your inbox for the verification link.');
    }
}
