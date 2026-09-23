<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_answered' => 'boolean',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }
}
