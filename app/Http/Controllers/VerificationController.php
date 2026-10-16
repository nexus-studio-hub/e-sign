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
                'reviews' => function ($query) {
                    $query->where('status', 'approved')
                        ->with('reviewer.reviewerRole')
                        ->orderBy('reviewed_at');
                }
            ])
            ->firstOrFail();
        
        $supplier = $document->requiredDocument->part->supplier;
        $part = $document->requiredDocument->part;
        
        // Generate QR code as base64 data URI
        $url = url('/verify/' . $serial);
        $qrCode = \Endroid\QrCode\QrCode::create($url)
            ->setSize(300)
            ->setMargin(10);
        
        $writer = new \Endroid\QrCode\Writer\PngWriter();
        $result = $writer->write($qrCode);
        $qrCodeDataUri = 'data:image/png;base64,' . base64_encode($result->getString());
        
        return Inertia::render('Verification/Show', [
            'serial' => $serial,
            'qrCodeImage' => $qrCodeDataUri,
            'verificationUrl' => $url,
            'supplier' => [
                'name' => $supplier->name,
                'code' => $supplier->code,
            ],
            'document' => [
                'name' => $document->requiredDocument->document_name,
                'status' => $document->status,
                'stamped_at' => $document->barcode_stamped_at,
            ],
            'part' => [
                'name' => $part->name,
                'part_no' => $part->part_no,
                'revision' => $document->latestVersion->version_number ?? 1,
            ],
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
