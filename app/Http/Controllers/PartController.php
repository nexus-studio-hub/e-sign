<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\RequiredDocument;
use App\Models\Document;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PartController extends Controller
{
    public function show($id)
    {
        $part = Part::with([
            'project',
            'supplier',
            'requiredDocuments.document.reviews.reviewer'
        ])->findOrFail($id);

        return Inertia::render('Parts/Show', [
            'part' => $part
        ]);
    }

    public function store(Request $request, $projectId)
    {
        $data = $request->validate([
            'part_number' => 'required',
            'part_name' => 'required',
            'model' => 'nullable',
            'supplier_id' => 'required|exists:suppliers,id',
            'documents' => 'required|array',
        ]);

        $part = Part::create([
            'project_id' => $projectId,
            'part_number' => $data['part_number'],
            'part_name' => $data['part_name'],
            'model' => $data['model'],
            'supplier_id' => $data['supplier_id'],
        ]);

        foreach ($request->documents as $doc) {
            $reqDoc = RequiredDocument::create([
                'project_id' => $projectId,
                'part_id' => $part->id,
                'document_name' => $doc['document_name'],
                'document_type' => $doc['document_type'],
                'is_required' => $doc['is_required'] ?? true,
                'start_date' => $doc['start_date'] ?? null,
                'due_date' => $doc['due_date'] ?? null,
            ]);

            Document::create([
                'project_id' => $projectId,
                'required_document_id' => $reqDoc->id,
                'status' => 'draft'
            ]);
        }

        // Notify supplier users about new part assignment
        $part->load(['supplier', 'project']);
        NotificationService::notifyPartAssigned($part);

        return back()->with('success', 'Part created successfully');
    }

    public function update(Request $request, $id)
    {
        $part = Part::findOrFail($id);
        
        $data = $request->validate([
            'part_number' => 'required',
            'part_name' => 'required',
            'model' => 'nullable',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);
        
        // Check if supplier changed
        $supplierChanged = $part->supplier_id != $data['supplier_id'];
        
        $part->update($data);
        
        // Notify new supplier if supplier changed
        if ($supplierChanged) {
            $part->load(['supplier', 'project']);
            NotificationService::notifyPartAssigned($part);
        }
        
        return back()->with('success', 'Part updated successfully');
    }

    public function destroy($id)
    {
        $part = Part::findOrFail($id);
        
        // Delete related documents and required documents
        $part->documents()->delete();
        $part->requiredDocuments()->delete();
        $part->delete();
        
        return back()->with('success', 'Part deleted successfully');
    }
}
