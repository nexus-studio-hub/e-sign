<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequiredDocument extends Model
{
    protected $fillable = ['project_id', 'part_id', 'document_name', 'document_type', 'is_required', 'start_date', 'due_date', 'description'];

    protected $casts = [
        'is_required' => 'boolean',
        'start_date' => 'date',
        'due_date' => 'date'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function part()
    {
        return $this->belongsTo(Part::class);
    }

    public function document()
    {
        return $this->hasOne(Document::class);
    }
}
