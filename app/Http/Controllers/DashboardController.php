<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Part;
use App\Models\Document;
use App\Models\Notification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        switch ($user->role) {
            case 'superadmin':
                return redirect()->route('admin.dashboard');
            case 'reviewer':
                return $this->reviewerDashboard($user);
            case 'supplier':
                return $this->supplierDashboard($user);
            default:
                abort(403, 'Unauthorized access');
        }
    }

    private function reviewerDashboard($user)
    {
        // Load user with role relationship
        $user->load('reviewerRole');
        
        if (!$user->reviewerRole) {
            abort(403, 'No role assigned');
        }
        
        $level = $user->reviewerRole->level;
        
        // Route to appropriate dashboard based on level
        switch ($level) {
            case 1:
                return $this->picDashboard($user);
            case 2:
                return $this->groupLeaderDashboard($user);
            case 3:
                return $this->managerDashboard($user);
            default:
                abort(403, 'Invalid role level');
        }
    }

    private function picDashboard($user)
    {
        $stats = [
            'totalProjects' => Project::count(),
            'draft' => Document::where('status', 'draft')->count(),
            'submitted' => Document::where('status', 'under_pic_review')->count(),
            'needRevision' => Document::where('status', 'need_revision')->count(),
        ];

        $projects = Project::with(['parts'])->latest()->paginate(10);

        return Inertia::render('Dashboard/PicDashboard', compact('stats', 'projects'));
    }

    private function supplierDashboard($user)
    {
        $stats = [
            'assignedProjects' => Part::where('supplier_id', $user->supplier_id)->count(),
            'pendingUpload' => Document::whereHas('requiredDocument.part', fn($q) => $q->where('supplier_id', $user->supplier_id))->where('status', 'draft')->count(),
            'submitted' => Document::whereHas('requiredDocument.part', fn($q) => $q->where('supplier_id', $user->supplier_id))->whereIn('status', ['under_pic_review', 'under_group_review', 'under_manager_review'])->count(),
            'needRevision' => Document::whereHas('requiredDocument.part', fn($q) => $q->where('supplier_id', $user->supplier_id))->where('status', 'need_revision')->count(),
        ];

        $parts = Part::where('supplier_id', $user->supplier_id)->with(['project', 'requiredDocuments.document'])->latest()->paginate(10);

        return Inertia::render('Dashboard/SupplierDashboard', compact('stats', 'parts'));
    }

    private function groupLeaderDashboard($user)
    {
        $stats = [
            'waitingReview' => Document::where('status', 'under_group_review')->count(),
            'approved' => Document::where('status', 'under_manager_review')->count(),
            'needRevision' => Document::where('status', 'need_revision')->count(),
        ];

        $documents = Document::where('status', 'under_group_review')->with(['project', 'requiredDocument'])->latest()->paginate(10);

        return Inertia::render('Dashboard/GroupLeaderDashboard', compact('stats', 'documents'));
    }

    private function managerDashboard($user)
    {
        $stats = [
            'waitingReview' => Document::where('status', 'under_manager_review')->count(),
            'approved' => Document::where('status', 'complete')->count(),
            'needRevision' => Document::where('status', 'need_revision')->count(),
        ];

        $documents = Document::where('status', 'under_manager_review')->with(['project', 'requiredDocument'])->latest()->paginate(10);

        return Inertia::render('Dashboard/ManagerDashboard', compact('stats', 'documents'));
    }
}
