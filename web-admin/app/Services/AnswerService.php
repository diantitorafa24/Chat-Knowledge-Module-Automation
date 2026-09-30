<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AnswerService
{
    private const SYSTEM_PROMPT = <<<'TEXT'
        Kamu adalah CIKA, asisten internal untuk staf NOC, teknisi, dan customer service.
        Konteks di bawah ini SUDAH DIPASTIKAN relevan dengan pertanyaan oleh sistem pencarian.
        Tugasmu HANYA merangkum dan menjawab langsung berdasarkan konteks tersebut, dengan bahasa yang jelas dan actionable.
        JANGAN pernah bilang "informasi tidak ditemukan" atau semacamnya - konteks yang diberikan pasti relevan.
        Jangan mengarang atau menambah informasi dari luar konteks.
        Jawab singkat, langsung ke tindakan, dalam Bahasa Indonesia.
        Di akhir jawaban, sebutkan sumbernya dalam format: (Sumber: <nama modul>, <bagian>).
        TEXT;

    public function answer(string $question, string $context): string
    {
        $key = trim((string) config('embedding.api_key'));

        if ($key === '') {
            throw new RuntimeException('GEMINI_API_KEY belum diisi di file .env.');
        }

        $model = config('rag.chat_model');
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent';

        $prompt = self::SYSTEM_PROMPT . "\n\nKonteks:\n" . $context . "\n\nPertanyaan: " . $question;

        $response = null;

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                $response = Http::withHeaders(['x-goog-api-key' => $key])
                    ->timeout(30)
                    ->post($url, [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['temperature' => 0.2, 'maxOutputTokens' => 500],
                    ]);
            } catch (ConnectionException $e) {
                if ($attempt === 3) {
                    throw new RuntimeException('Tidak bisa terhubung ke layanan Gemini.');
                }

                sleep($attempt * 5);

                continue;
            }

            if ($response->successful() || ! in_array($response->status(), [429, 500, 502, 503, 504], true) || $attempt === 3) {
                break;
            }

            sleep($attempt * 5);
        }

        if (! $response || ! $response->successful()) {
            $status = $response?->status() ?? 0;
            $detail = $response?->json('error.message') ?? 'tidak ada respons';

            throw new RuntimeException('Gemini error ' . $status . ': ' . mb_substr((string) $detail, 0, 200));
        }

        $text = $response->json('candidates.0.content.parts.0.text');

        if (! is_string($text) || trim($text) === '') {
            $reason = $response->json('candidates.0.finishReason', 'tidak diketahui');

            throw new RuntimeException('Gemini tidak mengembalikan jawaban teks (alasan: ' . $reason . ').');
        }

        return trim($text);
    }
}
