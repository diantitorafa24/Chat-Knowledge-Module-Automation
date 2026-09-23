<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentChunk extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
