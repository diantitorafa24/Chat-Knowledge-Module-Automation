<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentChunk;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ChunkingService
{
    public const MAX_CHARS = 1000;

    /**
     * Pecah blok hasil parser menjadi chunk dan simpan ke tabel document_chunks.
     * Chunk lama milik dokumen yang sama dihapus dulu, jadi aman untuk proses ulang.
     */
    public function chunk(Document $document, array $blocks): int
    {
        $document->loadMissing('module.category', 'versions');

        $module = $document->module;
        $version = $document->versions->first()?->version;
        $chunks = $this->buildChunks($blocks);

        if ($chunks === []) {
            throw new RuntimeException('Dokumen tidak menghasilkan chunk (hanya berisi judul tanpa isi).');
        }

        DB::transaction(function () use ($document, $module, $version, $chunks) {
            DocumentChunk::where('document_id', $document->id)->delete();

            foreach ($chunks as $i => $chunk) {
                DocumentChunk::create([
                    'document_id' => $document->id,
                    'module_id' => $document->module_id,
                    'chunk_index' => $i + 1,
                    'section' => $chunk['section'] === null ? null : mb_substr($chunk['section'], 0, 255),
                    'subsection' => $chunk['subsection'] === null ? null : mb_substr($chunk['subsection'], 0, 255),
                    'content' => $chunk['content'],
                    'metadata' => [
                        'module_code' => $module->code,
                        'module_name' => $module->name,
                        'category' => $module->category->name ?? null,
                        'version' => $version,
                        'document_name' => $document->original_filename,
                        'title' => $chunk['title'],
                        'heading_path' => $chunk['path'],
                        'char_count' => mb_strlen($chunk['content']),
                    ],
                ]);
            }
        });

        return count($chunks);
    }

    /**
     * Ubah daftar blok menjadi daftar chunk (belum disimpan).
     */
    public function buildChunks(array $blocks): array
    {
        $chunks = [];
        $title = null;
        $path = [];
        $buffer = [];

        $flush = function () use (&$chunks, &$buffer, &$title, &$path) {
            if ($buffer === []) {
                return;
            }

            foreach ($this->pack($buffer) as $content) {
                $chunks[] = [
                    'title' => $title,
                    'section' => $this->sectionOf($path, $title),
                    'subsection' => $this->subsectionOf($path),
                    'path' => array_values($path),
                    'content' => $content,
                ];
            }

            $buffer = [];
        };

        foreach ($blocks as $block) {
            // Block image sudah dikonversi ke paragraph oleh ProcessDocumentJob
            // Skip jika masih ada block image yang tidak terkonversi
            if ($block['type'] === 'image') {
                continue;
            }

            if ($block['type'] === 'heading') {
                $flush();
                $level = (int) $block['level'];

                if ($level === 0) {
                    $title = $block['text'];
                    $path = [];

                    continue;
                }

                foreach (array_keys($path) as $existing) {
                    if ($existing >= $level) {
                        unset($path[$existing]);
                    }
                }

                $path[$level] = $block['text'];

                continue;
            }

            $buffer = array_merge($buffer, $this->units($block));
        }

        $flush();

        return $chunks;
    }

    private function sectionOf(array $path, ?string $title): ?string
    {
        if ($path === []) {
            return $title;
        }

        ksort($path);

        return reset($path);
    }

    private function subsectionOf(array $path): ?string
    {
        if (count($path) < 2) {
            return null;
        }

        ksort($path);
        array_shift($path);

        return implode(' > ', $path);
    }

    private function units(array $block): array
    {
        return match ($block['type']) {
            'list_item' => [str_repeat('  ', (int) ($block['level'] ?? 0)) . '- ' . $block['text']],
            'table' => $this->tableUnits($block['rows']),
            default => [$block['text']],
        };
    }

    private function tableUnits(array $rows): array
    {
        if (count($rows) < 2) {
            return array_map(fn ($row) => implode(' | ', $row), $rows);
        }

        $header = array_shift($rows);
        $units = [];

        foreach ($rows as $row) {
            $parts = [];

            foreach ($row as $i => $cell) {
                if ($cell === '') {
                    continue;
                }

                $label = $header[$i] ?? '';
                $parts[] = $label !== '' ? $label . ': ' . $cell : $cell;
            }

            if ($parts !== []) {
                $units[] = implode('; ', $parts);
            }
        }

        return $units;
    }

    private function pack(array $units): array
    {
        $contents = [];
        $current = '';

        foreach ($units as $unit) {
            foreach ($this->splitLong($unit) as $piece) {
                $candidate = $current === '' ? $piece : $current . "\n" . $piece;

                if ($current !== '' && mb_strlen($candidate) > self::MAX_CHARS) {
                    $contents[] = $current;
                    $current = $piece;
                } else {
                    $current = $candidate;
                }
            }
        }

        if ($current !== '') {
            $contents[] = $current;
        }

        return $contents;
    }

    private function splitLong(string $text): array
    {
        if (mb_strlen($text) <= self::MAX_CHARS) {
            return [$text];
        }

        $sentences = preg_split('/(?<=[.!?;])\s+/u', $text);

        if ($sentences === false) {
            $sentences = [$text];
        }

        $pieces = [];
        $current = '';

        foreach ($sentences as $sentence) {
            while (mb_strlen($sentence) > self::MAX_CHARS) {
                if ($current !== '') {
                    $pieces[] = $current;
                    $current = '';
                }

                $pieces[] = mb_substr($sentence, 0, self::MAX_CHARS);
                $sentence = mb_substr($sentence, self::MAX_CHARS);
            }

            $candidate = $current === '' ? $sentence : $current . ' ' . $sentence;

            if ($current !== '' && mb_strlen($candidate) > self::MAX_CHARS) {
                $pieces[] = $current;
                $current = $sentence;
            } else {
                $current = $candidate;
            }
        }

        if ($current !== '') {
            $pieces[] = $current;
        }

        return $pieces;
    }
}
