<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Get all active users for demo accounts
        $demoAccounts = User::where('is_active', true)
            ->with('reviewerRole')
            ->orderByRaw("CASE 
                WHEN role = 'superadmin' THEN 1
                WHEN role = 'reviewer' THEN 2
                WHEN role = 'supplier' THEN 3
                ELSE 4
            END")
            ->get(['id', 'email', 'role', 'name', 'role_id'])
            ->map(function($user) {
                return [
                    'email' => $user->email,
                    'role' => $user->role === 'reviewer' && $user->reviewerRole 
                        ? $user->reviewerRole->name 
                        : ucfirst($user->role),
                    'name' => $user->name
                ];
            });
        
        return Inertia::render('Auth/Login', [
            'demoAccounts' => $demoAccounts
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => 'required|email', 'password' => 'required']);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
