<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    public $timestamps = false;

    protected $guarded = [];

    public function chatLog()
    {
        return $this->belongsTo(ChatLog::class);
    }
}
