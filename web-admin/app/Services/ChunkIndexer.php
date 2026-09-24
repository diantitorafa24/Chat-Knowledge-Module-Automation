<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentChunk;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class ChunkIndexer
{
    public function __construct(
        private EmbeddingService $embeddings,
        private QdrantService $qdrant,
    ) {
    }

    /**
     * Hitung embedding semua chunk milik dokumen, lalu simpan ke Qdrant (belum aktif).
     *
     * @param  callable(int, int): void|null  $onProgress  dipanggil dengan (selesai, total)
     */
    public function index(Document $document, ?callable $onProgress = null): int
    {
        $chunks = DocumentChunk::where('document_id', $document->id)->orderBy('chunk_index')->get();

        if ($chunks->isEmpty()) {
            throw new RuntimeException('Dokumen belum punya chunk untuk di-embed.');
        }

        $this->qdrant->ensureCollection();

        $points = [];
        $total = $chunks->count();

        foreach ($chunks as $i => $chunk) {
            $points[] = [
                'id' => $chunk->id,
                'vector' => $this->vectorFor($chunk),
                'payload' => [
                    'chunk_id' => $chunk->id,
                    'document_id' => $chunk->document_id,
                    'module_id' => $chunk->module_id,
                    'module_code' => $chunk->metadata['module_code'] ?? null,
                    'version' => $chunk->metadata['version'] ?? null,
                    'section' => $chunk->section,
                    'subsection' => $chunk->subsection,
                    'chunk_index' => $chunk->chunk_index,
                    'is_active' => false,
                ],
            ];

            if ($onProgress) {
                $onProgress($i + 1, $total);
            }
        }

        // Titik lama baru dihapus setelah semua embedding baru berhasil dihitung.
        $this->qdrant->deleteByDocument($document->id);

        foreach (array_chunk($points, 50) as $batch) {
            $this->qdrant->upsert($batch);
        }

        return $total;
    }

    private function vectorFor(DocumentChunk $chunk): array
    {
        $title = trim($chunk->section . ($chunk->subsection ? ' > ' . $chunk->subsection : ''));

        return Cache::remember(
            'embedding:' . config('embedding.model') . ':' . md5($title . '|' . $chunk->content),
            now()->addDays(30),
            fn () => $this->embeddings->embedDocument($chunk->content, $title !== '' ? $title : null)
        );
    }
}
