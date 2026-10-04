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
        $supplierUsers = $document->project->supplier->users;
        if ($supplierUsers->isEmpty()) return;

        $status = $review->status === 'approved' ? 'approved' : 'rejected';
        $title = "Document Reviewed";
        $message = "Your document '{$document->requiredDocument->document_name}' has been {$status} by {$review->reviewer->name}";
        $link = "/projects/{$document->project_id}";

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
        $pic = $document->project->pic;
        if (!$pic) return;

        $supplierName = $document->project->supplier->name;
        $documentName = $document->requiredDocument->document_name;
        
        $title = "Document Ready for Review";
        $message = "↓ in: {$supplierName} - {$documentName}";
        $link = "/reviews/{$document->id}";

        self::notify($pic->id, 'document_submitted', $title, $message, $link, [
            'document_id' => $document->id,
            'project_id' => $document->project_id,
        ]);
        
        self::logActivity($pic->id, 'document_received', "↓ in: {$supplierName} - {$documentName}", 'file-text');
    }

    public static function notifyProjectAssigned($project)
    {
        $supplierUsers = $project->supplier->users;
        if ($supplierUsers->isEmpty()) return;

        $title = "New Project Assigned";
        $message = "You have been assigned to project {$project->project_code}: {$project->project_name}";
        $link = "/projects/{$project->id}";

        foreach ($supplierUsers as $user) {
            self::notify($user->id, 'project_assigned', $title, $message, $link, [
                'project_id' => $project->id,
            ]);

            self::logActivity($user->id, 'project_assigned', "Assigned to project {$project->project_code}", 'file-text');
        }
    }

    public static function notifyNextReviewer($document)
    {
        $status = $document->status;
        $reviewer = null;

        // Determine next reviewer based on status
        if ($status === 'under_group_review') {
            $reviewer = \App\Models\User::where('role', 'group_leader')->first();
        } elseif ($status === 'under_manager_review') {
            $reviewer = \App\Models\User::where('role', 'manager')->first();
        }

        if (!$reviewer) return;

        $supplierName = $document->project->supplier->name;
        $documentName = $document->requiredDocument->document_name;
        
        $title = "Document Ready for Review";
        $message = "↓ in: {$supplierName} - {$documentName}";
        $link = "/reviews/{$document->id}";

        self::notify($reviewer->id, 'document_submitted', $title, $message, $link, [
            'document_id' => $document->id,
            'project_id' => $document->project_id,
        ]);
        
        self::logActivity($reviewer->id, 'document_received', "↓ in: {$supplierName} - {$documentName}", 'file-text');
    }
}
