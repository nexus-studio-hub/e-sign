<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Supplier;
use App\Models\Project;
use App\Models\Document;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'totalUsers' => User::count(),
            'totalSuppliers' => Supplier::count(),
            'totalProjects' => Project::count(),
            'totalDocuments' => Document::count(),
            'activeUsers' => User::where('is_active', true)->count(),
            'usersByRole' => User::select('role', DB::raw('count(*) as total'))
                ->groupBy('role')
                ->get()
                ->map(function($item) {
                    return [
                        'role' => ucfirst(str_replace('_', ' ', $item->role)),
                        'total' => $item->total
                    ];
                }),
        ];

        return Inertia::render('SuperAdmin/Dashboard', compact('stats'));
    }
}
