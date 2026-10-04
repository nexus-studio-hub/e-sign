<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    protected $fillable = ['project_code', 'project_name', 'model', 'part_number', 'part_name', 'supplier_id', 'pic_id', 'start_date', 'due_date', 'status', 'certificate_token'];

    protected $casts = ['start_date' => 'date', 'due_date' => 'date'];

    protected static function booted()
    {
        static::creating(function ($project) {
            if (empty($project->certificate_token)) {
                $project->certificate_token = (string) Str::uuid();
            }
        });
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    public function requiredDocuments()
    {
        return $this->hasMany(RequiredDocument::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function isFullyCompleted(): bool
    {
        $totalDocs = $this->documents()->count();
        if ($totalDocs === 0) return false;
        
        $completedDocs = $this->documents()->where('status', 'complete')->count();
        return $completedDocs === $totalDocs;
    }

    public function getCompletionPercentage(): int
    {
        $totalDocs = $this->documents()->count();
        if ($totalDocs === 0) return 0;
        
        $completedDocs = $this->documents()->where('status', 'complete')->count();
        return (int) round(($completedDocs / $totalDocs) * 100);
    }

    public function getAllApprovers()
    {
        return Review::whereHas('document', function($query) {
            $query->where('project_id', $this->id);
        })
        ->where('status', 'approved')
        ->with('reviewer')
        ->orderBy('reviewer_role')
        ->orderBy('reviewed_at')
        ->get()
        ->unique('reviewer_id')
        ->map(function($review) {
            return [
                'id' => $review->reviewer->id,
                'name' => $review->reviewer->name,
                'role' => $review->reviewer_role,
                'reviewed_at' => $review->reviewed_at->format('d M Y, H:i'),
            ];
        });
    }

    public function getCertificateUrl(): string
    {
        return route('certificate.verify', $this->certificate_token);
    }
}
