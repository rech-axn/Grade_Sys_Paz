<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            return Auth::user()->isTeacher() 
                ? redirect()->route('teacher.dashboard') 
                : redirect()->route('student.dashboard');
        }

        $prefillUser = $request->query('user', '');
        return view('auth.login', compact('prefillUser'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->isTeacher()) {
                return redirect()->intended(route('teacher.dashboard'))->with('success', 'Welcome back, ' . Auth::user()->name . '!');
            }

            return redirect()->intended(route('student.dashboard'))->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'You have been signed out successfully.');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return Auth::user()->isTeacher() 
                ? redirect()->route('teacher.dashboard') 
                : redirect()->route('student.dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:teacher,student'],
            'student_id_number' => ['required_if:role,student', 'nullable', 'string', 'unique:students,student_id_number'],
        ]);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        if ($validated['role'] === 'student') {
            $user->student()->create([
                'student_id_number' => $validated['student_id_number'],
                'course_section' => 'BSIT 1-A', // Default or could be added to form
                'year_level' => '1st Year',
                'gender' => 'Male',
            ]);
        }

        Auth::login($user);

        return $user->isTeacher()
            ? redirect()->route('teacher.dashboard')->with('success', 'Registration successful! Welcome, ' . $user->name . '.')
            : redirect()->route('student.dashboard')->with('success', 'Registration successful! Welcome, ' . $user->name . '.');
    }
}
