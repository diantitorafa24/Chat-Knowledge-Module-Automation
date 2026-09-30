<?php

namespace App\Http\Controllers;

use App\Models\ChatLog;

class ChatLogController extends Controller
{
    public function index()
    {
        $logs = ChatLog::with('module')->latest()->paginate(20);

        return view('chat_logs.index', ['logs' => $logs, 'unansweredOnly' => false]);
    }

    public function unanswered()
    {
        $logs = ChatLog::with('module')->where('is_answered', false)->latest()->paginate(20);

        return view('chat_logs.index', ['logs' => $logs, 'unansweredOnly' => true]);
    }
}
