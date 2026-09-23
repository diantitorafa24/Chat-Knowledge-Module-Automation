<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $guarded = [];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function chunks()
    {
        return $this->hasMany(DocumentChunk::class);
    }

    public function versions()
    {
        return $this->hasMany(ModuleVersion::class);
    }
}
