<?php

namespace App\Console\Commands;

use App\Models\DocumentChunk;
use App\Services\EmbeddingService;
use App\Services\QdrantService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class QdrantSearch extends Command
{
    protected $signature = 'qdrant:search {query : Pertanyaan yang dicari} {--top=5 : Jumlah hasil} {--module= : Batasi ke satu modul (ID)} {--all : Cari semua chunk, termasuk yang tidak aktif}';

    protected $description = 'Cari chunk lewat Qdrant (secara bawaan hanya chunk versi aktif)';

    public function handle(EmbeddingService $embeddings, QdrantService $qdrant): int
    {
        $query = (string) $this->argument('query');
        $module = $this->option('module') !== null ? (int) $this->option('module') : null;

        try {
            $vector = $embeddings->embedQuery($query);
            $filter = $this->option('all') ? [] : QdrantService::activeFilter($module);
            $hits = $qdrant->search($vector, max(1, (int) $this->option('top')), $filter);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info('Pertanyaan: ' . $query);
        $this->line('Filter    : ' . ($this->option('all') ? 'tanpa filter (semua chunk)' : 'hanya chunk aktif' . ($module ? ', modul ' . $module : '')));
        $this->newLine();

        if ($hits === []) {
            $this->warn('Tidak ada chunk aktif. Aktifkan sebuah versi modul dulu di Web Admin.');

            return self::SUCCESS;
        }

        foreach ($hits as $i => $hit) {
            $payload = $hit['payload'] ?? [];
            $chunk = DocumentChunk::find($payload['chunk_id'] ?? 0);

            $place = ($payload['section'] ?? null)
                ? $payload['section'] . (($payload['subsection'] ?? null) ? ' > ' . $payload['subsection'] : '')
                : '(tanpa bagian)';

            $this->line(sprintf(
                '#%d  skor %.4f  | %s  | %s  | aktif: %s',
                $i + 1,
                $hit['score'] ?? 0,
                $place,
                $payload['version'] ?? '-',
                ($payload['is_active'] ?? false) ? 'ya' : 'tidak'
            ));

            if ($chunk) {
                $this->line('     ' . Str::limit(str_replace("\n", ' / ', $chunk->content), 110));
            }
        }

        return self::SUCCESS;
    }
}
