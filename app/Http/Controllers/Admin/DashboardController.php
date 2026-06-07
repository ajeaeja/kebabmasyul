<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\Branch;
use App\Models\PartnerOrder;
use App\Models\BranchReport;
use App\Models\EditRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;

class DashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'role:admin',
        ];
    }

    /**
     * Admin Dashboard.
     */
    public function admin()
    {
        $totalPartners = Partner::count();
        $totalBranches = Branch::count();
        
        $recentOrders = PartnerOrder::with('partner')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $recentReports = BranchReport::with('branch')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $myPendingRequests = EditRequest::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.dashboard', compact(
            'totalPartners', 'totalBranches', 'recentOrders', 'recentReports', 'myPendingRequests'
        ));
    }
}
