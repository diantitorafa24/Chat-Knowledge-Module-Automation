<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ImageVisionService
{
    /**
     * Kirim gambar ke Gemini Vision dan dapatkan deskripsi teks.
     *
     * @param  string  $imageBase64  Base64-encoded image data
     * @param  string  $mimeType     e.g. image/png, image/jpeg
     * @param  string  $context      Nama modul/section sebagai konteks prompt
     */
    public function describe(string $imageBase64, string $mimeType, string $context = ''): string
    {
        $apiKey = trim((string) config('embedding.api_key'));

        if ($apiKey === '') {
            throw new RuntimeException('GEMINI_API_KEY belum diisi di file .env.');
        }

        $prompt = <<<TEXT
            Kamu sedang membantu sistem RAG (Retrieval-Augmented Generation) untuk dokumentasi teknis NOC (Network Operations Center).
            Gambar ini diambil dari modul SOP: {$context}.
            Jelaskan secara lengkap dan teknis apa yang ditampilkan dalam gambar ini:
            - Jika diagram/flowchart: jelaskan alurnya langkah demi langkah.
            - Jika screenshot UI/sistem: jelaskan setiap elemen dan fungsinya.
            - Jika foto fisik perangkat: identifikasi perangkat dan bagian-bagiannya.
            - Jika tabel/grafik: jelaskan data yang ditampilkan.
            Gunakan Bahasa Indonesia. Jawab langsung tanpa kata pembuka.
            TEXT;

        $response = Http::withHeaders(['x-goog-api-key' => $apiKey])
            ->timeout(60)
            ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-3.8-flash:generateContent', [
                'contents' => [[
                    'parts' => [
                        ['text' => $prompt],
                        ['inline_data' => [
                            'mime_type' => $mimeType,
                            'data'      => $imageBase64,
                        ]],
                    ],
                ]],
                'generationConfig' => ['temperature' => 0.1, 'maxOutputTokens' => 800],
            ]);

        if (! $response->successful()) {
            $detail = $response->json('error.message', 'tidak ada detail');
            Log::warning('Gemini Vision gagal', ['status' => $response->status(), 'detail' => $detail]);
            return '(Deskripsi gambar tidak tersedia)';
        }

        $text = $response->json('candidates.0.content.parts.0.text');

        return is_string($text) && trim($text) !== '' ? trim($text) : '(Deskripsi gambar tidak tersedia)';
    }
}
