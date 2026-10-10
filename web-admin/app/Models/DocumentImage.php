<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DocumentImage extends Model
{
    protected $fillable = [
        'document_id',
        'module_id',
        'image_index',
        'storage_path',
        'mime_type',
        'description',
        'section',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * URL publik gambar (via storage symlink).
     */
    public function publicUrl(): string
    {
        return Storage::url($this->storage_path);
    }
}
