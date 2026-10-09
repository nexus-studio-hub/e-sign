<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\DocumentReview;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DocumentController extends Controller
{
    public function show($id)
    {
        $document = Document::with([
            'project',
            'requiredDocument',
            'versions.uploader',
            'latestVersion',
            'reviews.reviewer'
        ])->findOrFail($id);

        // Redirect reviewers to review page (has lock UI and proper validation)
        if (auth()->check() && auth()->user()->role === 'reviewer') {
            return redirect()->route('reviews.show', $id);
        }

        return Inertia::render('Documents/Show', [
            'document' => $document
        ]);
    }

    public function upload(Request $request, $id)
    {
        \Log::info('Upload started', ['document_id' => $id, 'user_id' => $request->user()->id]);
        
        $request->validate(['file' => 'required|file|max:10240']);

        $document = Document::findOrFail($id);
        $file = $request->file('file');
        
        \Log::info('File received', ['file_name' => $file->getClientOriginalName(), 'size' => $file->getSize()]);
        
        // Get next version number
        $latestVersion = DocumentVersion::where('document_id', $document->id)
            ->max('version_number');
        $newVersion = $latestVersion ? $latestVersion + 1 : 1;
        
        \Log::info('Creating new version', ['version' => $newVersion]);
        
        // Create new version file
        $fileName = $document->project->project_code . '_' . $document->requiredDocument->document_type . '_v' . $newVersion . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('documents', $fileName, 'public');

        DocumentVersion::create([
            'document_id' => $document->id,
            'version_number' => $newVersion,
            'file_name' => $fileName,
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'uploaded_by' => $request->user()->id,
            'uploaded_at' => now(),
        ]);

        $document->update(['current_version' => $newVersion]);
        \Log::info('Version created successfully', ['version' => $newVersion, 'file' => $fileName]);

        // Auto-submit for review
        if ($document->status === 'draft' || $document->status === 'need_revision') {
            $document->update(['status' => 'under_pic_review']);
            
            // Load required relations before notification
            $document->load(['requiredDocument.part.supplier', 'project']);
            NotificationService::notifyDocumentSubmitted($document);
            \Log::info('Auto-submitted for review');
        }

        NotificationService::logActivity($request->user()->id, 'upload', "Uploaded {$document->requiredDocument->document_name}", 'upload');

        \Log::info('Upload completed successfully');
        return back()->with('success', 'File uploaded and submitted for review');
    }

    public function submit($id)
    {
        $document = Document::findOrFail($id);
        $document->update(['status' => 'under_pic_review']);

        // Load required relations before notification
        $document->load(['requiredDocument.part.supplier', 'project']);
        NotificationService::notifyDocumentSubmitted($document);
        NotificationService::logActivity(auth()->id(), 'document_submitted', "Submitted {$document->requiredDocument->document_name}", 'upload');

        return back()->with('success', 'Document submitted for review');
    }

    public function download($versionId)
    {
        $version = DocumentVersion::findOrFail($versionId);
        $filePath = storage_path('app/public/' . $version->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }
        
        return response()->download($filePath, $version->file_name);
    }

    public function preview($versionId)
    {
        $version = DocumentVersion::findOrFail($versionId);
        $filePath = storage_path('app/public/' . $version->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }
        
        return response()->file($filePath);
    }
}
