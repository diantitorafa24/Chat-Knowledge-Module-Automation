<?php

namespace App\Services;

use App\Models\DocumentChunk;
use App\Models\Module;

class RagSearchService
{
    public function __construct(
        private EmbeddingService $embeddings,
        private QdrantService $qdrant,
    ) {
    }

    /**
     * Cari chunk terbaik dari versi aktif untuk sebuah pertanyaan.
     */
    public function search(string $question, ?int $moduleId = null, ?int $limit = null): array
    {
        $question = trim($question);
        $limit = max(1, min($limit ?? \App\Models\Setting::get('rag_max_results', 3), 10));
        $minScore = (float) \App\Models\Setting::get('rag_min_score', 0.60);

        $vector = $this->embeddings->embedQuery($question);
        $hits = $this->qdrant->search($vector, $limit, QdrantService::activeFilter($moduleId));

        $chunkIds = collect($hits)
            ->map(fn ($hit) => (int) ($hit['payload']['chunk_id'] ?? $hit['id']))
            ->all();

        $chunks = DocumentChunk::whereIn('id', $chunkIds)->get()->keyBy('id');
        $modules = Module::with('category')
            ->whereIn('id', $chunks->pluck('module_id')->unique()->all())
            ->get()
            ->keyBy('id');

        $results = [];

        foreach ($hits as $hit) {
            $chunk = $chunks->get((int) ($hit['payload']['chunk_id'] ?? $hit['id']));

            // Titik yatim: chunk sudah dihapus dari MySQL tapi masih ada di Qdrant.
            if (! $chunk) {
                continue;
            }

            $module = $modules->get($chunk->module_id);

            $results[] = [
                'rank' => count($results) + 1,
                'score' => round((float) ($hit['score'] ?? 0), 4),
                'chunk_id' => $chunk->id,
                'module_id' => $chunk->module_id,
                'module_code' => $module->code ?? null,
                'module_name' => $module->name ?? null,
                'category' => $module?->category?->name,
                'version' => $chunk->metadata['version'] ?? null,
                'document_name' => $chunk->metadata['document_name'] ?? null,
                'section' => $chunk->section,
                'subsection' => $chunk->subsection,
                'content' => $chunk->content,
            ];
        }

        $relevant = array_values(array_filter($results, fn ($row) => $row['score'] >= $minScore));

        return [
            'question' => $question,
            'found' => $relevant !== [],
            'min_score' => $minScore,
            'top_score' => $results[0]['score'] ?? null,
            'results' => $results,
            'context' => $this->buildContext($relevant),
        ];
    }

    private function buildContext(array $results): string
    {
        $parts = [];

        foreach ($results as $i => $row) {
            $place = $row['section']
                ? $row['section'] . ($row['subsection'] ? ' > ' . $row['subsection'] : '')
                : null;

            $header = '[Sumber ' . ($i + 1) . '] ' . ($row['module_code'] ?? '') . ' - ' . ($row['module_name'] ?? '')
                . ' (' . ($row['version'] ?? '-') . ')' . ($place ? ' | ' . $place : '');

            $parts[] = $header . "\n" . $row['content'];
        }

        return implode("\n\n", $parts);
    }
}
