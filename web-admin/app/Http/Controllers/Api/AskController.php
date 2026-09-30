<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatLog;
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
            'telegram_user_id' => ['nullable', 'integer'],
            'username' => ['nullable', 'string', 'max:100'],
            'group_id' => ['nullable', 'integer'],
        ]);

        try {
            $search = $rag->search($data['question'], $data['module_id'] ?? null);
        } catch (\Throwable $e) {
            Log::error('Pencarian RAG gagal', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Pencarian gagal.', 'error' => $e->getMessage()], 503);
        }

        if (! $search['found']) {
            $answer = 'Maaf, informasi ini tidak ditemukan di modul yang tersedia.';

            $this->logChat($data, $search['question'], $answer, null, false);

            return response()->json([
                'question' => $search['question'],
                'found' => false,
                'answer' => $answer,
                'sources' => [],
            ]);
        }

        try {
            $answer = $ai->answer($search['question'], $search['context']);
        } catch (\Throwable $e) {
            Log::error('AnswerService gagal', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Gagal menyusun jawaban.', 'error' => $e->getMessage()], 503);
        }

        $moduleId = $search['results'][0]['module_id'] ?? null;
        $this->logChat($data, $search['question'], $answer, $moduleId, true);

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

    private function logChat(array $data, string $question, string $answer, ?int $moduleId, bool $isAnswered): void
    {
        try {
            ChatLog::create([
                'telegram_user_id' => $data['telegram_user_id'] ?? null,
                'username' => $data['username'] ?? null,
                'group_id' => $data['group_id'] ?? null,
                'question' => $question,
                'answer' => $answer,
                'module_id' => $moduleId,
                'is_answered' => $isAnswered,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal menyimpan chat log', ['error' => $e->getMessage()]);
        }
    }
}
