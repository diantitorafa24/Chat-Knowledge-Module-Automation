<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function versions()
    {
        return $this->hasMany(ModuleVersion::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function chunks()
    {
        return $this->hasMany(DocumentChunk::class);
    }

    public function chatLogs()
    {
        return $this->hasMany(ChatLog::class);
    }
}
