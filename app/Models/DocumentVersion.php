<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{
    protected $fillable = ['document_id', 'version_number', 'file_name', 'file_path', 'file_size', 'uploaded_by', 'uploaded_at'];

    protected $casts = ['uploaded_at' => 'datetime'];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
