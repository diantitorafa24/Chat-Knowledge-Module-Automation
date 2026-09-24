<?php

namespace App\Http\Controllers;

use App\Models\ChatLog;
use App\Models\Document;
use App\Models\DocumentChunk;
use App\Models\Module;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'Total Module' => Module::count(),
            'Total Documents' => Document::count(),
            'Total Chunks' => DocumentChunk::count(),
            'Pertanyaan Hari Ini' => ChatLog::whereDate('created_at', today())->count(),
            'Module Processing' => Module::where('status', 'processing')->count(),
        ];

        return view('dashboard.index', compact('stats'));
    }
}
