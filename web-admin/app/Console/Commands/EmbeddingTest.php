<?php

namespace App\Console\Commands;

use App\Services\EmbeddingService;
use Illuminate\Console\Command;

class EmbeddingTest extends Command
{
    protected $signature = 'embedding:test {text? : Kalimat yang diuji}';

    protected $description = 'Kirim satu kalimat ke Gemini dan tampilkan hasil embedding-nya';

    public function handle(EmbeddingService $embeddings): int
    {
        $text = $this->argument('text') ?: 'Internet pelanggan mati, apa yang harus dilakukan?';

        $this->info('Model: ' . config('embedding.model') . ' | Dimensi diminta: ' . config('embedding.dimensions'));
        $this->line('Teks: ' . $text);

        try {
            $start = microtime(true);
            $vector = $embeddings->embedQuery($text);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $ms = round((microtime(true) - $start) * 1000);
        $norm = sqrt(array_sum(array_map(fn ($v) => $v * $v, $vector)));

        $this->newLine();
        $this->info('Berhasil. Jumlah angka (dimensi): ' . count($vector) . ' | waktu: ' . $ms . ' ms');
        $this->line('5 angka pertama: ' . implode(', ', array_map(fn ($v) => number_format($v, 5), array_slice($vector, 0, 5))));
        $this->line('Panjang vector (norm): ' . number_format($norm, 4) . ' (mendekati 1 berarti sudah ternormalisasi)');

        return self::SUCCESS;
    }
}
