<?php

namespace App\Console\Commands;

use App\Models\DocumentChunk;
use App\Services\EmbeddingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class EmbeddingSearch extends Command
{
    protected $signature = 'embedding:search {query : Pertanyaan yang dicari} {--top=5 : Jumlah hasil}';

    protected $description = 'Uji pencarian makna: urutkan chunk berdasarkan kemiripan dengan pertanyaan';

    public function handle(EmbeddingService $embeddings): int
    {
        $chunks = DocumentChunk::with(['document', 'module'])
            ->orderBy('document_id')
            ->orderBy('chunk_index')
            ->get()
            ->unique(fn ($chunk) => md5($chunk->content));

        if ($chunks->isEmpty()) {
            $this->error('Belum ada chunk. Proses dokumen dulu lewat Web Admin.');

            return self::FAILURE;
        }

        $query = (string) $this->argument('query');

        try {
            $this->info('Pertanyaan: ' . $query);
            $queryVector = $embeddings->embedQuery($query);

            $this->line('Menghitung embedding ' . $chunks->count() . ' chunk (yang sudah pernah dihitung diambil dari cache)...');

            $scored = [];

            foreach ($chunks as $chunk) {
                $title = trim($chunk->section . ($chunk->subsection ? ' > ' . $chunk->subsection : ''));

                $vector = Cache::remember(
                    'embedding:' . config('embedding.model') . ':' . md5($title . '|' . $chunk->content),
                    now()->addDay(),
                    fn () => $embeddings->embedDocument($chunk->content, $title !== '' ? $title : null)
                );

                $scored[] = ['chunk' => $chunk, 'score' => EmbeddingService::cosine($queryVector, $vector)];
            }
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);

        $this->newLine();

        foreach (array_slice($scored, 0, max(1, (int) $this->option('top'))) as $rank => $row) {
            $chunk = $row['chunk'];
            $place = $chunk->section
                ? $chunk->section . ($chunk->subsection ? ' > ' . $chunk->subsection : '')
                : '(tanpa bagian)';

            $this->line(sprintf(
                '#%d  skor %.4f  | %s  | %s chunk %d',
                $rank + 1,
                $row['score'],
                $place,
                $chunk->metadata['version'] ?? '-',
                $chunk->chunk_index
            ));
            $this->line('     ' . Str::limit(str_replace("\n", ' / ', $chunk->content), 110));
        }

        return self::SUCCESS;
    }
}
