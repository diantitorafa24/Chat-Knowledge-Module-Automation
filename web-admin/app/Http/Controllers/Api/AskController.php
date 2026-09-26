<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AnswerService;
use App\Services\RagSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AskController extends Controller
{
    public function ask(Request $request, RagSearchService $rag, AnswerService $ai)
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'min:2', 'max:' . (int) config('rag.max_question_length')],
            'module_id' => ['nullable', 'integer', 'exists:modules,id'],
        ]);

        try {
            $search = $rag->search($data['question'], $data['module_id'] ?? null);
        } catch (\Throwable $e) {
            Log::error('Pencarian RAG gagal', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Pencarian gagal.', 'error' => $e->getMessage()], 503);
        }

        if (! $search['found']) {
            return response()->json([
                'question' => $search['question'],
                'found' => false,
                'answer' => 'Maaf, informasi ini tidak ditemukan di modul yang tersedia.',
                'sources' => [],
            ]);
        }

        try {
            $answer = $ai->answer($search['question'], $search['context']);
        } catch (\Throwable $e) {
            Log::error('AnswerService gagal', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Gagal menyusun jawaban.', 'error' => $e->getMessage()], 503);
        }

        return response()->json([
            'question' => $search['question'],
            'found' => true,
            'answer' => $answer,
            'sources' => array_map(fn ($r) => [
                'module_code' => $r['module_code'],
                'module_name' => $r['module_name'],
                'version' => $r['version'],
                'section' => $r['section'],
                'subsection' => $r['subsection'],
                'score' => $r['score'],
            ], $search['results']),
        ]);
    }
}
