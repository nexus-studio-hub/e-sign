<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    protected $fillable = ['project_id', 'part_number', 'part_name', 'model', 'supplier_id'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function requiredDocuments()
    {
        return $this->hasMany(RequiredDocument::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
