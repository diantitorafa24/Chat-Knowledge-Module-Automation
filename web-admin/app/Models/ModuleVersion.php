<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleVersion extends Model
{
    protected $guarded = [];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
