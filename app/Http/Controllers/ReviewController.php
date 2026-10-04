<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Review;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function show($id)
    {
        $document = Document::with(['project', 'requiredDocument', 'latestVersion', 'reviews.reviewer'])->findOrFail($id);
        return Inertia::render('Documents/Review', compact('document'));
    }

    public function approve(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        $user = $request->user();

        // Check if review already exists
        $review = Review::where('document_id', $document->id)
            ->where('reviewer_id', $user->id)
            ->where('reviewer_role', $user->role)
            ->first();

        if ($review) {
            // Update existing review
            $review->update([
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
                'reviewer_role' => $user->role,
                'status' => 'approved',
                'comment' => $request->comment,
                'reviewed_at' => now(),
            ]);
        }

        $newStatus = match($user->role) {
            'pic_development' => 'under_group_review',
            'group_leader' => 'under_manager_review',
            'manager' => 'complete',
        };

        $document->update(['status' => $newStatus]);

        NotificationService::notifyDocumentReviewed($document, $review);
        NotificationService::logActivity($user->id, 'review_approved', "Approved {$document->requiredDocument->document_name}", 'check-circle');

        return redirect("/projects/{$document->project_id}")->with('success', 'Document approved');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['comment' => 'required']);

        $document = Document::findOrFail($id);
        $user = $request->user();

        // Check if review already exists
        $review = Review::where('document_id', $document->id)
            ->where('reviewer_id', $user->id)
            ->where('reviewer_role', $user->role)
            ->first();

        if ($review) {
            // Update existing review
            $review->update([
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
                'reviewer_role' => $user->role,
                'status' => 'rejected',
                'comment' => $request->comment,
                'reviewed_at' => now(),
            ]);
        }

        $document->update(['status' => 'need_revision']);

        NotificationService::notifyDocumentReviewed($document, $review);
        NotificationService::logActivity($user->id, 'review_rejected', "Rejected {$document->requiredDocument->document_name}", 'x-circle');

        return redirect("/projects/{$document->project_id}")->with('success', 'Document rejected');
    }
}
