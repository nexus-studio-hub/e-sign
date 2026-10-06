<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentReview extends Model
{
    protected $fillable = [
        'document_id',
        'reviewer_id',
        'status',
        'comment',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
