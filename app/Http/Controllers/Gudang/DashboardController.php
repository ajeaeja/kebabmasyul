<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\RawMaterial;
use App\Models\IncomingStock;
use App\Models\PartnerOrder;
use Illuminate\Routing\Controllers\HasMiddleware;

class DashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'role:gudang',
        ];
    }

    /**
     * Tim Gudang/Supervisor Dashboard.
     * Note: NO pricing, NO financial data.
     */
    public function gudang()
    {
        // 1. Stock warning levels
        $safetyStockAlerts = RawMaterial::whereColumn('stock', '<=', 'safety_stock')->get();
        $safetyAlertCount = $safetyStockAlerts->count();

        // 2. Core raw materials stock lists
        $rawMaterials = RawMaterial::orderBy('stock', 'asc')->get();

        // 3. Incoming stock transactions history (recent 5)
        $recentIncoming = IncomingStock::with('rawMaterial')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        // 4. Partner orders in progress (menunggu_dipacking, dipacking, dikirim) for status updates
        $activeOrders = PartnerOrder::with('partner')
            ->whereIn('status', ['menunggu_dipacking', 'dipacking', 'dikirim'])
            ->orderBy('id', 'desc')
            ->get();

        return view('gudang.dashboard', compact(
            'safetyStockAlerts', 'safetyAlertCount', 'rawMaterials', 'recentIncoming', 'activeOrders'
        ));
    }
}
