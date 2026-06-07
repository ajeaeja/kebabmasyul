<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Redirect to the correct dashboard based on role.
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->isOwner()) {
            return redirect()->route('dashboard.owner');
        } elseif ($user->isGudang()) {
            return redirect()->route('dashboard.gudang');
        } else {
            return redirect()->route('dashboard.admin');
        }
    }
}
