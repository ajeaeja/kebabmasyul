<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function pollNotifications()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['totalBadgeCount' => 0, 'html' => '']);
        }

        $safetyStockCount = 0;
        $safetyStockItems = [];
        if ($user->isOwner() || $user->isGudang()) {
            $safetyStockItems = \App\Models\RawMaterial::whereColumn('stock', '<=', 'safety_stock')->get();
            $safetyStockCount = $safetyStockItems->count();
        }

        $notifications = \App\Models\Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationsCount = $notifications->count();
        $totalBadgeCount = $notificationsCount + $safetyStockCount;

        // Render snippet view
        $html = view('layouts._notification_items', compact(
            'user', 'notifications', 'notificationsCount', 'safetyStockCount', 'safetyStockItems', 'totalBadgeCount'
        ))->render();

        return response()->json([
            'totalBadgeCount' => $totalBadgeCount,
            'html' => $html,
            'notifications' => $notifications
        ]);
    }

    public function readAndRedirect(Notification $notification)
    {
        $notification->update(['is_read' => true]);
        
        if ($notification->link) {
            return redirect($notification->link);
        }
        
        return back();
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())->update(['is_read' => true]);
        return back()->with('success', 'Semua notifikasi ditandai telah dibaca.');
    }
}
