<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Inertia\Inertia;

class VerificationController extends Controller
{
    public function show($serial)
    {
        $document = Document::where('barcode_serial', $serial)
            ->with([
                'requiredDocument.part.supplier',
                'project',
                'versions.uploader',
                'reviews' => function ($query) {
                    $query->where('status', 'approved')
                        ->with('reviewer.reviewerRole')
                        ->orderBy('reviewed_at');
                }
            ])
            ->firstOrFail();
        
        $supplier = $document->requiredDocument->part->supplier;
        $part = $document->requiredDocument->part;
        
        return Inertia::render('Verification/Show', [
            'serial' => $serial,
            'supplier' => [
                'company_name' => $supplier->company_name,
                'code' => $supplier->code,
                'contact_person' => $supplier->contact_person,
                'email' => $supplier->email,
                'phone' => $supplier->phone,
            ],
            'document' => [
                'name' => $document->requiredDocument->document_name,
                'type' => $document->requiredDocument->document_type,
                'status' => $document->status,
                'stamped_at' => $document->barcode_stamped_at,
            ],
            'part' => [
                'name' => $part->part_name,
                'number' => $part->part_number,
                'model' => $part->model,
                'revision' => $document->latestVersion->version_number ?? 1,
            ],
            'project' => [
                'name' => $document->project->project_name,
                'code' => $document->project->project_code,
            ],
            'uploadHistory' => $document->versions->map(function ($version) {
                return [
                    'version' => $version->version_number,
                    'file_name' => $version->file_name,
                    'file_size' => round($version->file_size / 1024, 2) . ' KB',
                    'uploaded_by' => $version->uploader->name,
                    'uploaded_at' => $version->uploaded_at,
                ];
            }),
            'approvals' => $document->reviews->map(function ($review) {
                return [
                    'level' => $review->reviewer->reviewerRole->level ?? 0,
                    'role_name' => $review->reviewer->reviewerRole->name ?? 'Unknown',
                    'reviewer_name' => $review->reviewer->name,
                    'reviewer_nik' => $review->reviewer->nik ?? 'N/A',
                    'reviewed_at' => $review->reviewed_at,
                    'comment' => $review->comment ?? null,
                ];
            }),
        ]);
    }
}
