<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Supplier;
use App\Models\RequiredDocument;
use App\Models\Document;
use App\Models\Notification;
use App\Models\ActivityLog;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        
        DB::beginTransaction();
        try {
            // Collect all document IDs for this project
            $documentIds = [];
            
            // Delete all related data in correct order (child → parent)
            foreach ($project->parts as $part) {
                foreach ($part->requiredDocuments as $reqDoc) {
                    if ($reqDoc->document) {
                        $documentIds[] = $reqDoc->document->id;
                        
                        // Delete all version files from storage
                        foreach ($reqDoc->document->versions as $version) {
                            if ($version->file_path && Storage::disk('public')->exists($version->file_path)) {
                                Storage::disk('public')->delete($version->file_path);
                            }
                        }
                        
                        // Delete database records (child → parent)
                        $reqDoc->document->reviews()->delete();
                        $reqDoc->document->versions()->delete();
                        $reqDoc->document->delete();
                    }
                }
                
                // Delete required documents
                $part->requiredDocuments()->delete();
                
                // Delete notifications related to this part
                Notification::where('data->part_id', $part->id)->delete();
            }
            
            // Delete parts
            $project->parts()->delete();
            
            // Delete notifications related to documents in this project
            if (!empty($documentIds)) {
                Notification::whereJsonContains('data->document_id', $documentIds)->delete();
                foreach ($documentIds as $docId) {
                    Notification::where('data->document_id', $docId)->delete();
                }
            }
            
            // Delete activity logs related to this project (check data column for project_id or document_id)
            if (!empty($documentIds)) {
                ActivityLog::whereJsonContains('data->document_id', $documentIds)->delete();
                foreach ($documentIds as $docId) {
                    ActivityLog::where('data->document_id', $docId)->delete();
                }
            }
            
            // Delete project
            $project->delete();
            
            DB::commit();
            
            return redirect()->route('projects.index')
                ->with('success', 'Project and all related data deleted successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete project: ' . $e->getMessage());
        }
    }
}
