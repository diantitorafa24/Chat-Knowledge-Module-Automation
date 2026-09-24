<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class EmbeddingService
{
    /**
     * Embedding untuk isi chunk yang akan disimpan (dokumen).
     */
    public function embedDocument(string $content, ?string $title = null): array
    {
        $title = ($title !== null && $title !== '') ? $title : 'none';

        return $this->embed('title: ' . $title . ' | text: ' . $content);
    }

    /**
     * Embedding untuk pertanyaan pengguna.
     */
    public function embedQuery(string $query): array
    {
        return $this->embed('task: question answering | query: ' . $query);
    }

    /**
     * Kirim satu teks ke Gemini, kembalikan vector berisi angka.
     */
    public function embed(string $text): array
    {
        $key = config('embedding.api_key');

        if (! $key) {
            throw new RuntimeException('GEMINI_API_KEY belum diisi di file .env.');
        }

        $model = config('embedding.model');
        $dimensions = (int) config('embedding.dimensions');
        $url = rtrim(config('embedding.endpoint'), '/') . '/models/' . $model . ':embedContent';

        $response = null;

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                $response = Http::withHeaders(['x-goog-api-key' => $key])
                    ->timeout(30)
                    ->post($url, [
                        'model' => 'models/' . $model,
                        'content' => ['parts' => [['text' => $text]]],
                        'output_dimensionality' => $dimensions,
                    ]);
            } catch (ConnectionException $e) {
                if ($attempt === 3) {
                    throw new RuntimeException('Tidak bisa terhubung ke layanan Gemini. Periksa koneksi internet.');
                }

                sleep($attempt * 2);

                continue;
            }

            if ($response->successful()) {
                break;
            }

            if (! in_array($response->status(), [429, 500, 502, 503, 504], true) || $attempt === 3) {
                break;
            }

            sleep($attempt * 2);
        }

        if (! $response || ! $response->successful()) {
            throw new RuntimeException($this->errorMessage($response));
        }

        $values = $response->json('embedding.values') ?? $response->json('embeddings.0.values');

        if (! is_array($values) || $values === []) {
            throw new RuntimeException('Respons Gemini tidak berisi vector embedding. Kunci respons: ' . implode(', ', array_keys((array) $response->json())));
        }

        if (count($values) !== $dimensions) {
            throw new RuntimeException('Jumlah dimensi ' . count($values) . ' tidak sama dengan yang diharapkan (' . $dimensions . ').');
        }

        return array_map('floatval', $values);
    }

    /**
     * Kemiripan dua vector (1 = sangat mirip, 0 = tidak berhubungan).
     */
    public static function cosine(array $a, array $b): float
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($a as $i => $value) {
            $other = $b[$i] ?? 0.0;
            $dot += $value * $other;
            $normA += $value * $value;
            $normB += $other * $other;
        }

        if ($normA == 0.0 || $normB == 0.0) {
            return 0.0;
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }

    private function errorMessage(?Response $response): string
    {
        if (! $response) {
            return 'Layanan Gemini tidak memberi respons.';
        }

        $status = $response->status();
        $detail = (string) $response->json('error.message', '');

        $hint = match (true) {
            in_array($status, [400, 401, 403], true) => ' Periksa GEMINI_API_KEY di file .env.',
            $status === 404 => ' Nama model tidak dikenali. Periksa GEMINI_EMBEDDING_MODEL.',
            $status === 429 => ' Batas pemakaian tercapai. Tunggu beberapa saat lalu coba lagi.',
            default => '',
        };

        return 'Gemini error ' . $status . ': ' . mb_substr($detail, 0, 200) . $hint;
    }
}
