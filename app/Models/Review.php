<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['document_id', 'document_version_id', 'reviewer_id', 'reviewer_role', 'status', 'comment', 'reviewed_at'];

    protected $casts = ['reviewed_at' => 'datetime'];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function documentVersion()
    {
        return $this->belongsTo(DocumentVersion::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
