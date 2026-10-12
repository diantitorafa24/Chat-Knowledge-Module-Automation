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
            'telegram_user_id' => ['nullable'],
            'username' => ['nullable', 'string', 'max:100'],
            'group_id' => ['nullable'],
        ]);

        $qLower = strtolower(trim($data['question']));
        // Bersihkan kata 'cika' dan tanda baca untuk deteksi sapaan
        $cleanedGreeting = trim(preg_replace('/[^\w\s]/u', '', preg_replace('/\bcika\b/i', '', $qLower)));
        $greetings = ['', 'hai', 'halo', 'hello', 'p', 'ping', 'hi', 'tes', 'test', 'pagi', 'siang', 'sore', 'malam', 'assalamualaikum'];

        if (in_array($cleanedGreeting, $greetings)) {
            $answer = "Halo! Saya CIKA, asisten AI internal untuk seluruh divisi di Disacloud. Ada yang bisa saya bantu terkait SOP, panduan, atau operasional hari ini?";
            $log = $this->logChat($data, $data['question'], $answer, null, true);
            return response()->json([
                'question' => $data['question'],
                'found' => true,
                'answer' => $answer,
                'sources' => [],
                'image_urls' => [],
                'chat_log_id' => $log?->id,
            ]);
        }

        // Untuk pencarian RAG, gunakan pertanyaan bersih tanpa kata panggil 'cika' agar embedding lebih tajam
        $searchQuery = trim(preg_replace('/\bcika\b/i', '', $data['question']));
        if ($searchQuery === '') {
            $searchQuery = $data['question'];
        }

        try {
            $search = $rag->search($searchQuery, $data['module_id'] ?? null);
        } catch (\Throwable $e) {
            Log::error('Pencarian RAG gagal', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Pencarian gagal.', 'error' => $e->getMessage()], 503);
        }

        if (! $search['found']) {
            $answer = 'Maaf, informasi ini tidak ditemukan di modul yang tersedia.';
            $log = $this->logChat($data, $search['question'], $answer, null, false);

            return response()->json([
                'question' => $search['question'],
                'found' => false,
                'answer' => $answer,
                'sources' => [],
                'image_urls' => [],
                'chat_log_id' => $log?->id,
            ]);
        }

        try {
            $answer = $ai->answer($search['question'], $search['context']);
        } catch (\Throwable $e) {
            Log::error('AnswerService gagal', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Gagal menyusun jawaban.', 'error' => $e->getMessage()], 503);
        }

        $moduleId = $search['results'][0]['module_id'] ?? null;
        $log = $this->logChat($data, $search['question'], $answer, $moduleId, true);

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
            'image_urls' => $search['image_urls'] ?? [],
            'chat_log_id' => $log?->id,
        ]);
    }

    private function logChat(array $data, string $question, string $answer, ?int $moduleId, bool $isAnswered): ?ChatLog
    {
        try {
            return ChatLog::create([
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

            return null;
        }
    }
}
