<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequiredDocument extends Model
{
    protected $fillable = ['project_id', 'document_name', 'document_type', 'is_required', 'description'];

    protected $casts = ['is_required' => 'boolean'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function document()
    {
        return $this->hasOne(Document::class);
    }
}
