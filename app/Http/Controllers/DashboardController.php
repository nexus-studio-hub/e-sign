<?php

namespace App\Http\Controllers;

use App\Models\Project;
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
            case 'pic_development':
                return $this->picDashboard($user);
            case 'supplier':
                return $this->supplierDashboard($user);
            case 'group_leader':
                return $this->groupLeaderDashboard($user);
            case 'manager':
                return $this->managerDashboard($user);
        }
    }

    private function picDashboard($user)
    {
        $stats = [
            'totalProjects' => Project::where('pic_id', $user->id)->count(),
            'draft' => Document::whereHas('project', fn($q) => $q->where('pic_id', $user->id))->where('status', 'draft')->count(),
            'submitted' => Document::whereHas('project', fn($q) => $q->where('pic_id', $user->id))->where('status', 'under_pic_review')->count(),
            'needRevision' => Document::whereHas('project', fn($q) => $q->where('pic_id', $user->id))->where('status', 'need_revision')->count(),
        ];

        $projects = Project::where('pic_id', $user->id)->with(['supplier', 'documents'])->latest()->paginate(10);

        return Inertia::render('Dashboard/PicDashboard', compact('stats', 'projects'));
    }

    private function supplierDashboard($user)
    {
        $stats = [
            'assignedProjects' => Project::where('supplier_id', $user->supplier_id)->count(),
            'pendingUpload' => Document::whereHas('project', fn($q) => $q->where('supplier_id', $user->supplier_id))->where('status', 'draft')->count(),
            'submitted' => Document::whereHas('project', fn($q) => $q->where('supplier_id', $user->supplier_id))->whereIn('status', ['under_pic_review', 'under_group_review', 'under_manager_review'])->count(),
            'needRevision' => Document::whereHas('project', fn($q) => $q->where('supplier_id', $user->supplier_id))->where('status', 'need_revision')->count(),
        ];

        $projects = Project::where('supplier_id', $user->supplier_id)->with(['pic', 'documents'])->latest()->paginate(10);

        return Inertia::render('Dashboard/SupplierDashboard', compact('stats', 'projects'));
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
