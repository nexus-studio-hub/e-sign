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
        $query = Project::with(['supplier', 'pic']);

        if ($user->role === 'supplier') {
            $query->where('supplier_id', $user->supplier_id);
        } elseif ($user->role === 'pic_development') {
            $query->where('pic_id', $user->id);
        }

        // Server-side search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('project_code', 'like', "%{$search}%")
                  ->orWhere('project_name', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $projects = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Projects/Index', compact('projects'));
    }

    public function show($id)
    {
        $project = Project::with(['supplier', 'pic', 'requiredDocuments.document.latestVersion', 'documents'])->findOrFail($id);
        
        $approvers = $project->getAllApprovers();

        return Inertia::render('Projects/Show', [
            'project' => array_merge($project->toArray(), [
                'approvers' => $approvers
            ])
        ]);
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 'active')->get();
        return Inertia::render('Projects/Create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_code' => 'required|unique:projects',
            'project_name' => 'required',
            'model' => 'required',
            'part_number' => 'required',
            'part_name' => 'required',
            'supplier_id' => 'required|exists:suppliers,id',
            'start_date' => 'required|date',
            'due_date' => 'required|date',
            'documents' => 'required|array',
        ]);

        $project = Project::create(array_merge($data, ['pic_id' => $request->user()->id, 'status' => 'active']));

        foreach ($request->documents as $doc) {
            $reqDoc = RequiredDocument::create([
                'project_id' => $project->id,
                'document_name' => $doc['document_name'],
                'document_type' => $doc['document_type'],
                'is_required' => $doc['is_required'] ?? true,
            ]);

            Document::create(['project_id' => $project->id, 'required_document_id' => $reqDoc->id, 'status' => 'draft']);
        }

        // Notify supplier users about new project assignment
        NotificationService::notifyProjectAssigned($project);

        // Log activity for PIC
        NotificationService::logActivity($request->user()->id, 'project_created', "Created project {$project->project_code}", 'file-text');

        return redirect()->route('projects.show', $project->id);
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
            'model' => 'required',
            'part_number' => 'required',
            'part_name' => 'required',
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
