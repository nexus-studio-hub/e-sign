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
        
        // Check if version already exists
        $existingVersion = DocumentVersion::where('document_id', $document->id)
            ->where('version_number', 1)
            ->first();
        
        if ($existingVersion) {
            \Log::info('Replacing existing version', ['version_id' => $existingVersion->id]);
            
            // Replace existing file
            $fileName = $existingVersion->file_name;
            $path = $existingVersion->file_path;
            
            // Delete old file
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                \Log::info('Old file deleted', ['path' => $path]);
            }
            
            // Store new file with same name
            $storedPath = $file->storeAs('documents', $fileName, 'public');
            \Log::info('New file stored', ['file_name' => $fileName, 'stored_path' => $storedPath, 'actual_size' => Storage::disk('public')->size($storedPath)]);
            
            // Update version record
            $existingVersion->update([
                'file_size' => $file->getSize(),
                'uploaded_by' => $request->user()->id,
                'uploaded_at' => now(),
            ]);
            \Log::info('Version record updated');
        } else {
            \Log::info('Creating first version');
            
            // Create first version
            $version = 1;
            $fileName = $document->project->project_code . '_' . $document->requiredDocument->document_type . '_v' . $version . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $fileName, 'public');

            DocumentVersion::create([
                'document_id' => $document->id,
                'version_number' => $version,
                'file_name' => $fileName,
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'uploaded_by' => $request->user()->id,
                'uploaded_at' => now(),
            ]);

            $document->update(['current_version' => $version]);
        }

        // Auto-submit for review
        if ($document->status === 'draft' || $document->status === 'need_revision') {
            $document->update(['status' => 'under_pic_review']);
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

    public function review(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,need_revision',
            'comment' => 'nullable|string'
        ]);

        $document = Document::findOrFail($id);
        $user = $request->user();

        DocumentReview::create([
            'document_id' => $document->id,
            'reviewer_id' => $user->id,
            'status' => $request->status,
            'comment' => $request->comment,
        ]);

        if ($request->status === 'approved') {
            $reviewerRole = $user->reviewerRole;
            if ($reviewerRole->level == 3) {
                $document->update(['status' => 'approved']);
            } elseif ($reviewerRole->level == 2) {
                $document->update(['status' => 'under_manager_review']);
            } elseif ($reviewerRole->level == 1) {
                $document->update(['status' => 'under_group_review']);
            }
        } else {
            $document->update(['status' => 'need_revision']);
        }

        NotificationService::logActivity($user->id, 'document_reviewed', "Reviewed {$document->requiredDocument->document_name}", 'review');

        return back()->with('success', 'Review submitted successfully');
    }
}
