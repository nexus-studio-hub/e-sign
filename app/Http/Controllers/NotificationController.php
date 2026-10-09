<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->limit(10)
            ->get();
        
        $unreadCount = auth()->user()
            ->notifications()
            ->unread()
            ->count();
        
        // Check if request expects JSON (from fetch API)
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'notifications' => $notifications,
                'unread_count' => $unreadCount,
            ]);
        }
        
        // For Inertia requests, return Inertia response
        return inertia('Notifications/Index', [
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }
    
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return back();
    }
    
    public function markAllAsRead()
    {
        auth()->user()->notifications()->unread()->update(['read_at' => now()]);
        
        return back();
    }
}
