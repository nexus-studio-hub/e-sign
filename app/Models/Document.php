<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['project_id', 'required_document_id', 'current_version', 'status'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function requiredDocument()
    {
        return $this->belongsTo(RequiredDocument::class);
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function reviews()
    {
        return $this->hasMany(DocumentReview::class)->orderBy('created_at', 'desc');
    }

    public function latestVersion()
    {
        return $this->hasOne(DocumentVersion::class)->latestOfMany('version_number');
    }
}
