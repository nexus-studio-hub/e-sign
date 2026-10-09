<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\ActivityLog;

class NotificationService
{
    public static function notify($userId, $type, $title, $message, $link = null, $data = [])
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'data' => $data,
        ]);
    }

    public static function logActivity($userId, $type, $action, $icon = 'file', $data = [])
    {
        return ActivityLog::create([
            'user_id' => $userId,
            'type' => $type,
            'action' => $action,
            'icon' => $icon,
            'data' => $data,
        ]);
    }

    public static function notifyDocumentReviewed($document, $review)
    {
        // Get supplier users through part relationship
        $supplier = $document->requiredDocument->part->supplier;
        $supplierUsers = $supplier->users;
        
        if ($supplierUsers->isEmpty()) return;

        $status = $review->status === 'approved' ? 'approved' : 'rejected';
        $title = "Document Reviewed";
        $message = "Your document '{$document->requiredDocument->document_name}' has been {$status} by {$review->reviewer->name}";
        $link = "/documents/{$document->id}";

        foreach ($supplierUsers as $supplierUser) {
            self::notify($supplierUser->id, "review_{$status}", $title, $message, $link, [
                'document_id' => $document->id,
                'review_id' => $review->id,
            ]);

            self::logActivity($supplierUser->id, 'review_received', "Received {$status} review", 'check-circle');
        }
    }

    public static function notifyDocumentSubmitted($document)
    {
        // Get all reviewers with level 1
        $level1Reviewers = \App\Models\User::where('role', 'reviewer')
            ->whereHas('reviewerRole', function($q) {
                $q->where('level', 1);
            })
            ->get();
        
        if ($level1Reviewers->isEmpty()) return;

        // Get supplier through relationship chain
        $supplier = $document->requiredDocument->part->supplier;
        $supplierName = $supplier->company_name;
        $documentName = $document->requiredDocument->document_name;
        
        $title = "Document Ready for Review";
        $message = "↓ in: {$supplierName} - {$documentName}";
        $link = "/documents/{$document->id}";

        // Notify ALL level 1 reviewers
        foreach ($level1Reviewers as $reviewer) {
            self::notify($reviewer->id, 'document_submitted', $title, $message, $link, [
                'document_id' => $document->id,
                'supplier_id' => $supplier->id,
            ]);
            
            self::logActivity($reviewer->id, 'document_received', "↓ in: {$supplierName} - {$documentName}", 'file-text');
        }
    }

    public static function notifyPartAssigned($part)
    {
        // Get all users from the supplier
        $supplier = $part->supplier;
        $supplierUsers = $supplier->users;
        
        if ($supplierUsers->isEmpty()) return;

        $project = $part->project;
        $title = "New Part Assigned";
        $message = "Part {$part->part_number} ({$part->part_name}) has been assigned to {$supplier->company_name} in project {$project->project_code}";
        $link = "/parts/{$part->id}";

        // Notify ALL supplier users
        foreach ($supplierUsers as $user) {
            self::notify($user->id, 'part_assigned', $title, $message, $link, [
                'part_id' => $part->id,
                'project_id' => $project->id,
                'supplier_id' => $supplier->id,
            ]);

            self::logActivity($user->id, 'part_assigned', "Assigned to part {$part->part_number} in project {$project->project_code}", 'file-text');
        }
    }

    public static function notifyFinalApproval($document)
    {
        // Notify supplier when Level 3 approves (final approval)
        $supplier = $document->requiredDocument->part->supplier;
        $supplierUsers = $supplier->users;
        
        if ($supplierUsers->isEmpty()) return;

        $title = "Document Fully Approved";
        $message = "Your document '{$document->requiredDocument->document_name}' has been fully approved and is now complete";
        $link = "/documents/{$document->id}";

        foreach ($supplierUsers as $user) {
            self::notify($user->id, 'document_approved', $title, $message, $link, [
                'document_id' => $document->id,
            ]);

            self::logActivity($user->id, 'document_approved', "Document {$document->requiredDocument->document_name} fully approved", 'check-circle');
        }
    }

    public static function notifyNextReviewer($document)
    {
        $status = $document->status;
        $reviewers = collect();

        // Determine next reviewers based on status using LEVEL
        if ($status === 'under_group_review') {
            // Get all reviewers with level 2
            $reviewers = \App\Models\User::where('role', 'reviewer')
                ->whereHas('reviewerRole', function($q) {
                    $q->where('level', 2);
                })
                ->get();
        } elseif ($status === 'under_manager_review') {
            // Get all reviewers with level 3
            $reviewers = \App\Models\User::where('role', 'reviewer')
                ->whereHas('reviewerRole', function($q) {
                    $q->where('level', 3);
                })
                ->get();
        }

        if ($reviewers->isEmpty()) return;

        // Get supplier through relationship chain
        $supplier = $document->requiredDocument->part->supplier;
        $supplierName = $supplier->company_name;
        $documentName = $document->requiredDocument->document_name;
        
        $title = "Document Ready for Review";
        $message = "↓ in: {$supplierName} - {$documentName}";
        $link = "/documents/{$document->id}";

        // Notify ALL reviewers at this level
        foreach ($reviewers as $reviewer) {
            self::notify($reviewer->id, 'document_submitted', $title, $message, $link, [
                'document_id' => $document->id,
                'supplier_id' => $supplier->id,
            ]);
            
            self::logActivity($reviewer->id, 'document_received', "↓ in: {$supplierName} - {$documentName}", 'file-text');
        }
    }
}
