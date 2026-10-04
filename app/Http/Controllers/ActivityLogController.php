<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $activities = auth()->user()
            ->activityLogs()
            ->latest()
            ->limit(5)
            ->get();
        
        // Check if request expects JSON (from fetch API)
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['activities' => $activities]);
        }
        
        // For Inertia requests, return Inertia response
        return inertia('Activities/Index', [
            'activities' => $activities,
        ]);
    }
}
