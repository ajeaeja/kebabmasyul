<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectDashboard(Auth::user());
        }
        return view('auth.login');
    }

    /**
     * Process authentication.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            return $this->redirectDashboard(Auth::user());
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Process logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }

    /**
     * Helper to redirect based on user role.
     */
    private function redirectDashboard($user)
    {
        if ($user->isOwner()) {
            return redirect()->route('dashboard.owner');
        } elseif ($user->isGudang()) {
            return redirect()->route('dashboard.gudang');
        } else {
            return redirect()->route('dashboard.admin');
        }
    }
}
