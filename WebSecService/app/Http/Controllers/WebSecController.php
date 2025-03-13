<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class WebSecController extends Controller
{
    // Register a new user and log them in
    public function register(Request $request)
    {
        // Validate the incoming request data.
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Create the new user in the database.
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Optionally, log the user in immediately after registration.
        Auth::login($user);

        // Redirect to a dashboard or home page.
        return redirect('/dashboard')->with('message', 'Registration successful!');
    }

    // Log in an existing user
    public function login(Request $request)
    {
        // Validate the login credentials.
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to authenticate the user.
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        // Authentication failed.
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    // Log out the current user.
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('message', 'Logged out successfully!');
    }
}
