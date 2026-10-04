<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentVersion;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DocumentController extends Controller
{
    public function upload(Request $request, $id)
    {
        $request->validate(['file' => 'required|file|max:10240']);

        $document = Document::findOrFail($id);
        $file = $request->file('file');
        
        $version = $document->current_version + 1;
        $fileName = $document->project->project_code . '_' . $document->requiredDocument->document_type . '_v' . $version . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('documents', $fileName);

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

        NotificationService::logActivity($request->user()->id, 'upload', "Uploaded {$document->requiredDocument->document_name}", 'upload');

        return back()->with('success', 'File uploaded successfully');
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
        $filePath = storage_path('app/' . $version->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }
        
        return response()->download($filePath, $version->file_name);
    }
}
