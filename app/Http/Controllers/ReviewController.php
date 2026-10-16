<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Review;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReviewController extends Controller
{
    private function validateSequentialApproval($document, $reviewerLevel)
    {
        // Level 1 can always review
        if ($reviewerLevel == 1) {
            return true;
        }
        
        // Level 2 needs Level 1 approval
        if ($reviewerLevel == 2) {
            $level1Approved = Review::where('document_id', $document->id)
                ->where('document_version_id', $document->latestVersion->id)
                ->where('reviewer_role', 'pic_development')
                ->where('status', 'approved')
                ->exists();
            
            if (!$level1Approved) {
                throw new \Exception('Level 1 must approve this document first');
            }
        }
        
        // Level 3 needs Level 2 approval
        if ($reviewerLevel == 3) {
            $level2Approved = Review::where('document_id', $document->id)
                ->where('document_version_id', $document->latestVersion->id)
                ->where('reviewer_role', 'group_leader')
                ->where('status', 'approved')
                ->exists();
            
            if (!$level2Approved) {
                throw new \Exception('Level 2 must approve this document first');
            }
        }
        
        return true;
    }

    public function show($id)
    {
        $document = Document::with([
            'project', 
            'requiredDocument.part', 
            'latestVersion',
            'versions.uploader'
        ])->findOrFail($id);
        
        // Get ALL reviews with reviewer and document version
        $allReviews = Review::where('document_id', $id)
            ->with(['reviewer.reviewerRole', 'documentVersion'])
            ->orderBy('reviewed_at', 'desc')
            ->get()
            ->map(function($review) {
                return [
                    'id' => $review->id,
                    'document_id' => $review->document_id,
                    'document_version_id' => $review->document_version_id,
                    'reviewer_id' => $review->reviewer_id,
                    'reviewer_role' => $review->reviewer_role,
                    'status' => $review->status,
                    'comment' => $review->comment,
                    'reviewed_at' => $review->reviewed_at?->format('Y-m-d H:i:s'),
                    'reviewer' => [
                        'id' => $review->reviewer->id,
                        'name' => $review->reviewer->name,
                        'email' => $review->reviewer->email,
                    ],
                    'document_version' => [
                        'version_number' => $review->documentVersion?->version_number,
                    ],
                ];
            })
            ->toArray();
        
        return Inertia::render('Documents/Review', [
            'document' => $document,
            'allReviews' => $allReviews,
        ]);
    }

    public function approve(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        $user = $request->user();

        // Validate sequential approval
        try {
            $this->validateSequentialApproval($document, $user->reviewerRole->level);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        // Check if document is at correct status for this reviewer level
        $expectedStatus = match($user->reviewerRole->level) {
            1 => 'under_pic_review',
            2 => 'under_group_review',
            3 => 'under_manager_review',
            default => null,
        };

        if ($document->status !== $expectedStatus) {
            return back()->with('error', 'Document is not at your review level');
        }

        // Check if review already exists
        $review = Review::where('document_id', $document->id)
            ->where('reviewer_id', $user->id)
            ->where('reviewer_role', $user->reviewerRole?->code)
            ->first();

        if ($review) {
            // Update existing review
            $review->update([
                'document_version_id' => $document->latestVersion->id,
                'status' => 'approved',
                'comment' => $request->comment,
                'reviewed_at' => now(),
            ]);
        } else {
            // Create new review
            $review = Review::create([
                'document_id' => $document->id,
                'document_version_id' => $document->latestVersion->id,
                'reviewer_id' => $user->id,
                'reviewer_role' => $user->reviewerRole?->code ?? 'unknown',
                'status' => 'approved',
                'comment' => $request->comment,
                'reviewed_at' => now(),
            ]);
        }

        $newStatus = match($user->reviewerRole->code) {
            'pic_development' => 'under_group_review',
            'group_leader' => 'under_manager_review',
            'manager' => 'complete',
        };

        $document->update(['status' => $newStatus]);

        // Load required relations before notification
        $document->load(['requiredDocument.part.supplier', 'project']);
        
        // Send notifications based on new status
        if ($newStatus === 'complete') {
            // Final approval (Level 3) - notify supplier
            NotificationService::notifyFinalApproval($document);
            
            // Trigger barcode stamping
            try {
                app(\App\Services\BarcodeService::class)->stampDocument($document);
            } catch (\Exception $e) {
                \Log::error('Barcode stamping failed: ' . $e->getMessage());
            }
        } else {
            // Notify next level reviewers only
            NotificationService::notifyNextReviewer($document);
        }
        
        NotificationService::logActivity($user->id, 'review_approved', "Approved {$document->requiredDocument->document_name}", 'check-circle');

        $partId = $document->requiredDocument->part_id;
        return redirect("/parts/{$partId}")->with('success', 'Document approved');
    }

    public function reject(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        $user = $request->user();

        // Validate sequential approval
        try {
            $this->validateSequentialApproval($document, $user->reviewerRole->level);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        // Check if document is at correct status for this reviewer level
        $expectedStatus = match($user->reviewerRole->level) {
            1 => 'under_pic_review',
            2 => 'under_group_review',
            3 => 'under_manager_review',
            default => null,
        };

        if ($document->status !== $expectedStatus) {
            return back()->with('error', 'Document is not at your review level');
        }

        // Check if review already exists
        $review = Review::where('document_id', $document->id)
            ->where('reviewer_id', $user->id)
            ->where('reviewer_role', $user->reviewerRole?->code)
            ->first();

        if ($review) {
            // Update existing review
            $review->update([
                'document_version_id' => $document->latestVersion->id,
                'status' => 'rejected',
                'comment' => $request->comment,
                'reviewed_at' => now(),
            ]);
        } else {
            // Create new review
            $review = Review::create([
                'document_id' => $document->id,
                'document_version_id' => $document->latestVersion->id,
                'reviewer_id' => $user->id,
                'reviewer_role' => $user->reviewerRole?->code ?? 'unknown',
                'status' => 'rejected',
                'comment' => $request->comment,
                'reviewed_at' => now(),
            ]);
        }

        $document->update(['status' => 'need_revision']);

        // Load required relations before notification
        $document->load(['requiredDocument.part.supplier', 'project']);
        NotificationService::notifyDocumentReviewed($document, $review);
        NotificationService::logActivity($user->id, 'review_rejected', "Rejected {$document->requiredDocument->document_name}", 'x-circle');

        $partId = $document->requiredDocument->part_id;
        return redirect("/parts/{$partId}")->with('success', 'Document rejected');
    }
}
