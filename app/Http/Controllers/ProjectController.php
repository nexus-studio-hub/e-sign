<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Supplier;
use App\Models\RequiredDocument;
use App\Models\Document;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Project::withCount('parts');

        // Filter for supplier: only show projects with parts assigned to them
        if ($user->role === 'supplier') {
            $query->whereHas('parts', function($q) use ($user) {
                $q->where('supplier_id', $user->supplier_id);
            });
        }

        // Server-side search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('project_code', 'like', "%{$search}%")
                  ->orWhere('project_name', 'like', "%{$search}%");
            });
        }

        $projects = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Projects/Index', compact('projects'));
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        $project = Project::with(['parts.supplier', 'parts.requiredDocuments.document.latestVersion'])->findOrFail($id);
        
        // Filter parts for supplier: only show parts assigned to them
        if ($user->role === 'supplier') {
            $project->load(['parts' => function($q) use ($user) {
                $q->where('supplier_id', $user->supplier_id);
            }, 'parts.supplier', 'parts.requiredDocuments.document.latestVersion']);
        }
        
        $suppliers = Supplier::where('status', 'active')->get();
        
        return Inertia::render('Projects/Show', [
            'project' => $project,
            'suppliers' => $suppliers
        ]);
    }

    public function create()
    {
        // Not needed anymore - using modal
        return redirect()->route('projects.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_code' => 'required|unique:projects',
            'project_name' => 'required',
        ]);

        $project = Project::create(array_merge($data, ['status' => 'active']));

        return redirect()->route('projects.index')->with('success', 'Project created successfully');
    }

    public function edit($id)
    {
        $project = Project::with(['supplier', 'requiredDocuments'])->findOrFail($id);
        $suppliers = Supplier::where('status', 'active')->get();
        
        return Inertia::render('Projects/Edit', compact('project', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        
        $data = $request->validate([
            'project_code' => 'required|unique:projects,project_code,' . $id,
            'project_name' => 'required',
            'supplier_id' => 'required|exists:suppliers,id',
            'start_date' => 'required|date',
            'due_date' => 'required|date',
            'status' => 'required|in:active,completed,cancelled',
        ]);
        
        $project->update($data);
        
        return redirect()->route('projects.show', $project->id)->with('success', 'Project updated successfully');
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        
        // Check if project has documents with status != draft
        $hasActiveDocuments = $project->documents()->whereNotIn('status', ['draft'])->exists();
        
        if ($hasActiveDocuments) {
            return back()->with('error', 'Cannot delete project with active documents. Please cancel or complete all documents first.');
        }
        
        // Delete related data
        $project->documents()->delete();
        $project->requiredDocuments()->delete();
        $project->delete();
        
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully');
    }
}
