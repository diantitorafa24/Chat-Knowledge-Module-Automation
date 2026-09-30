<?php

namespace App\Http\Controllers;

use App\Models\ChatLog;
use App\Models\Document;
use App\Models\DocumentChunk;
use App\Models\Module;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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

        $trend = collect(range(6, 0))->map(function ($daysAgo) {
            $date = today()->subDays($daysAgo);

            return [
                'label' => $date->format('d/m'),
                'total' => ChatLog::whereDate('created_at', $date)->count(),
                'answered' => ChatLog::whereDate('created_at', $date)->where('is_answered', true)->count(),
            ];
        });

        $topModules = ChatLog::select('module_id', DB::raw('count(*) as total'))
            ->whereNotNull('module_id')
            ->groupBy('module_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('module')
            ->get();

        $totalChats = ChatLog::count();
        $unansweredCount = ChatLog::where('is_answered', false)->count();
        $unansweredRate = $totalChats > 0 ? round($unansweredCount / $totalChats * 100, 1) : 0;

        $health = [
            'qdrant' => $this->checkQdrant(),
            'gemini' => $this->checkGemini(),
        ];

        return view('dashboard.index', compact('stats', 'trend', 'topModules', 'unansweredCount', 'unansweredRate', 'totalChats', 'health'));
    }

    private function checkQdrant(): array
    {
        try {
            $res = Http::timeout(3)->get(rtrim((string) config('qdrant.url'), '/') . '/');

            return ['ok' => $res->successful(), 'message' => $res->successful() ? 'Terhubung' : 'Merespons tapi error'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Tidak bisa dihubungi'];
        }
    }

    private function checkGemini(): array
    {
        $key = trim((string) config('embedding.api_key'));

        if ($key === '') {
            return ['ok' => false, 'message' => 'API key belum diisi'];
        }

        return ['ok' => true, 'message' => 'API key tersimpan'];
    }
}
