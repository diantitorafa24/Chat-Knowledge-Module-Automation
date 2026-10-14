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
            'role' => ['nullable', 'string', 'max:50'],
            'module_id' => ['nullable', 'integer', 'exists:modules,id'],
            'telegram_user_id' => ['nullable'],
            'username' => ['nullable', 'string', 'max:100'],
            'group_id' => ['nullable'],
        ]);

        $rawQuestion = $data['question'];

        // 1. Ekstrak Role / Divisi
        $detectedRole = $data['role'] ?? null;

        // Cek pola eksplisit: role: ... atau divisi: ... (case-insensitive)
        if (! $detectedRole && preg_match('/(?:role|divisi)\s*[:=]\s*([a-zA-Z0-9_\-\s]+)/i', $rawQuestion, $m)) {
            $detectedRole = trim($m[1]);
        }

        // Cek tag dalam kurung siku: [teknisi], [noc], dst.
        if (! $detectedRole && preg_match('/\[(noc|teknisi|engineer|cs|customer service|admin|administrasi|network)\]/i', $rawQuestion, $m)) {
            $detectedRole = trim($m[1]);
        }

        // Bersihkan teks pertanyaan dari kata panggil 'cika' dan penanda role/divisi
        $cleanedText = preg_replace('/\bcika\b/i', '', $rawQuestion);
        $cleanedText = preg_replace('/(?:role|divisi)\s*[:=]\s*[a-zA-Z0-9_\-\s]+/i', '', $cleanedText);
        $cleanedText = preg_replace('/\[(noc|teknisi|engineer|cs|customer service|admin|administrasi|network)\]/i', '', $cleanedText);
        $searchQuery = trim($cleanedText);
        if ($searchQuery === '') {
            $searchQuery = $rawQuestion;
        }

        // Deteksi sapaan
        $cleanedGreeting = trim(preg_replace('/[^\w\s]/u', '', strtolower($searchQuery)));
        $greetingPatterns = [
            'hai', 'halo', 'hello', 'p', 'ping', 'hi', 'tes', 'test',
            'pagi', 'siang', 'sore', 'malam', 'assalamualaikum', 'assalamu alaikum',
            'selamat pagi', 'selamat siang', 'selamat sore', 'selamat malam',
            'apa kabar', 'halo apa kabar', 'hai apa kabar', 'gimana kabar', 'gimana kabarnya', 'kabar'
        ];

        $isGreeting = in_array($cleanedGreeting, $greetingPatterns, true)
            || (preg_match('/^(hai|halo|hello|hi|selamat (pagi|siang|sore|malam)|assalamu\s*alaikum|apa kabar)/i', $cleanedGreeting) && count(explode(' ', $cleanedGreeting)) <= 4);

        if ($isGreeting || $cleanedGreeting === '') {
            $answer = "Halo! Saya CIKA, asisten AI internal untuk seluruh divisi di Disacloud. Kabar saya baik dan siap membantu Anda. Ada yang bisa saya bantu terkait SOP, panduan teknis, atau operasional hari ini?";
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

        // 2. Auto-Detect Role / Divisi jika belum ditentukan eksplisit
        if (! $detectedRole) {
            $qCheck = strtolower($searchQuery);

            $teknisiKeywords = [
                'splicing', 'splice', 'sambung kabel', 'sambungin', 'kabel putus', 'tarik kabel',
                'dropcore', 'precon', 'cleaver', 'stripper', 'fusion splicer', 'odp', 'los merah',
                'patch cord', 'pigtail', 'tiang', 'fo putus', 'vfl', 'senter optik', 'kabel fiber',
                'kabel optik', 'kabel fo', 'tang kupas', 'protection sleeve'
            ];
            $nocKeywords = [
                'winbox', 'mikrotik', 'pppoe', 'secret', 'disable user', 'enable user', 're-aktivasi',
                'olt', 'onu', 'vlan', 'ip address', 'bgp', 'routing', 'gateway', 'rx power',
                'redaman optik', 'mrtg', 'nms', 'bandwidth', 'limitasi'
            ];
            $csKeywords = [
                'komplain', 'keluhan', 'tagihan', 'billing', 'pembayaran', 'paket internet', 'pasang baru', 'psb'
            ];

            foreach ($teknisiKeywords as $kw) {
                if (str_contains($qCheck, $kw)) {
                    $detectedRole = 'Teknisi';
                    break;
                }
            }

            if (! $detectedRole) {
                foreach ($nocKeywords as $kw) {
                    if (str_contains($qCheck, $kw)) {
                        $detectedRole = 'NOC';
                        break;
                    }
                }
            }

            if (! $detectedRole) {
                foreach ($csKeywords as $kw) {
                    if (str_contains($qCheck, $kw)) {
                        $detectedRole = 'Customer Service';
                        break;
                    }
                }
            }
        }

        // 3. Cocokkan Role ke Kategori Modul
        $targetCategory = null;
        $categoryModuleIds = null;
        if ($detectedRole) {
            $rLower = strtolower(trim($detectedRole));
            $targetCategory = \App\Models\Category::where('name', 'like', "%{$rLower}%")->first();

            if (! $targetCategory && in_array($rLower, ['engineer', 'lapangan', 'teknis'])) {
                $targetCategory = \App\Models\Category::where('name', 'like', '%teknisi%')->first();
            }

            if ($targetCategory) {
                $categoryModuleIds = \App\Models\Module::where('category_id', $targetCategory->id)
                    ->where('status', 'active')
                    ->pluck('id')
                    ->all();

                // Jika divisi ini BELUM memiliki modul aktif sama sekali:
                if (empty($categoryModuleIds)) {
                    $answer = "Informasi untuk divisi {$targetCategory->name} belum tersedia di dalam modul resmi.";
                    $log = $this->logChat($data, $searchQuery, $answer, null, false);

                    return response()->json([
                        'question' => $searchQuery,
                        'role' => $targetCategory->name,
                        'found' => false,
                        'answer' => $answer,
                        'sources' => [],
                        'image_urls' => [],
                        'chat_log_id' => $log?->id,
                    ]);
                }
            }
        }

        // 4. Lakukan pencarian RAG (terisolasi ke modul kategori jika ada)
        try {
            $search = $rag->search($searchQuery, $data['module_id'] ?? null, null, $categoryModuleIds);
        } catch (\Throwable $e) {
            Log::error('Pencarian RAG gagal', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Pencarian gagal.', 'error' => $e->getMessage()], 503);
        }

        if (! $search['found']) {
            $answer = 'Maaf, informasi ini tidak ditemukan di modul yang tersedia.';
            $log = $this->logChat($data, $search['question'], $answer, null, false);

            return response()->json([
                'question' => $search['question'],
                'role' => $targetCategory?->name ?? $detectedRole,
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

        // Validasi apakah AI mendeteksi bahwa konteks modul sebenarnya tidak menjawab pertanyaan
        if (str_contains(strtoupper($answer), 'NOT_FOUND_IN_MODULE')) { // dengan atau tanpa kurung siku
            $answer = 'Maaf, informasi spesifik ini tidak ditemukan di modul yang tersedia.';
            $log = $this->logChat($data, $search['question'], $answer, null, false);

            return response()->json([
                'question' => $search['question'],
                'role' => $targetCategory?->name ?? $detectedRole,
                'found' => false,
                'answer' => $answer,
                'sources' => [],
                'image_urls' => [],
                'chat_log_id' => $log?->id,
            ]);
        }

        $moduleId = $search['results'][0]['module_id'] ?? null;
        $log = $this->logChat($data, $search['question'], $answer, $moduleId, true);

        return response()->json([
            'question' => $search['question'],
            'role' => $targetCategory?->name ?? $detectedRole,
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
