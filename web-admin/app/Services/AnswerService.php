<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AnswerService
{
    private const SYSTEM_PROMPT = <<<'TEXT'
        Kamu adalah CIKA (Chat Intelligence & Knowledge Assistant), asisten AI internal resmi untuk seluruh divisi di Disacloud.
        Tugas utamamu adalah memberikan jawaban yang SANGAT CERMAT, AKURAT, LENGKAP, dan BERKUALITAS TINGGI berdasarkan modul yang disediakan.

        PRINSIP KECERMATAN & AKURASI JAWABAN:
        1. Jawab langsung ke inti pertanyaan dengan runtut, jelas, dan sistematis.
        2. CERMAT TERHADAP DATA TEKNIS: Jika pertanyaan berkaitan dengan daftar IP address, port, nama site, kredensial (username/password), perintah CLI/script, atau tabel konfigurasi, sebutkan secara LENGKAP dan DETAIL satu per satu sesuai konteks tanpa ada yang disingkat atau dihilangkan.
        3. Pahami modul secara mendalam: jika suatu prosedur memiliki prasyarat (seperti VPN harus konek dulu), urutan langkah, catatan peringatan, atau perbedaan khusus per site/perangkat, jelaskan secara terstruktur.
        4. Jangan mengarang informasi di luar konteks modul yang diberikan.
        5. Gunakan Bahasa Indonesia yang profesional, ramah, dan jelas.

        ATURAN FORMAT TELEGRAM (WAJIB DIIKUTI AGAR TIDAK ERROR):
        - Gunakan teks biasa saja yang rapi dan mudah dibaca di layar HP.
        - DILARANG menggunakan simbol Markdown formatting seperti **, *, _, `, #, ~, >, [, ].
        - Gunakan penomoran jelas untuk langkah (contoh: "1. Langkah Pertama").
        - Gunakan strip untuk daftar rincian (contoh: "- 172.31.0.10:61928 untuk Disa (R. RB)").
        - Gunakan HURUF KAPITAL seperlunya untuk penekanan (contoh: "CATATAN PENTING:").
        - Di baris paling akhir, cantumkan sumber: (Sumber: nama modul, bab/bagian).
        TEXT;

    public function answer(string $question, string $context): string
    {
        $key = trim((string) config('embedding.api_key'));

        if ($key === '') {
            throw new RuntimeException('GEMINI_API_KEY belum diisi di file .env.');
        }

        $model = \App\Models\Setting::get('ai_chat_model', config('rag.chat_model'));
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent';

        $systemPrompt = \App\Models\Setting::get('ai_system_prompt', self::SYSTEM_PROMPT);
        $prompt = $systemPrompt . "\n\nKonteks Modul:\n" . $context . "\n\nPertanyaan: " . $question;

        $response = null;

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                $response = Http::withHeaders(['x-goog-api-key' => $key])
                    ->timeout(45)
                    ->post($url, [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['temperature' => 0.2, 'maxOutputTokens' => 2048],
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
