<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class QdrantService
{
    public function collection(): string
    {
        return (string) config('qdrant.collection');
    }

    /**
     * Pastikan koleksi dan indeks label ada. Mengembalikan true kalau koleksi baru dibuat.
     */
    public function ensureCollection(): bool
    {
        $created = false;
        $dimensions = (int) config('embedding.dimensions');

        if (! $this->collectionExists()) {
            $this->request('PUT', '/collections/' . $this->collection(), [
                'vectors' => [
                    'size' => $dimensions,
                    'distance' => 'Cosine',
                ],
            ]);

            $created = true;
        } else {
            $info = $this->request('GET', '/collections/' . $this->collection());
            $size = $info['result']['config']['params']['vectors']['size'] ?? null;

            if ($size !== null && (int) $size !== $dimensions) {
                throw new RuntimeException('Ukuran vector di koleksi (' . $size . ') tidak sama dengan pengaturan embedding (' . $dimensions . ').');
            }
        }

        foreach (['module_id' => 'integer', 'document_id' => 'integer', 'is_active' => 'bool'] as $field => $schema) {
            $this->request('PUT', '/collections/' . $this->collection() . '/index?wait=true', [
                'field_name' => $field,
                'field_schema' => $schema,
            ]);
        }

        return $created;
    }

    public function collectionExists(): bool
    {
        $result = $this->request('GET', '/collections/' . $this->collection() . '/exists');

        return (bool) ($result['result']['exists'] ?? false);
    }

    /**
     * Simpan atau perbarui titik. Setiap titik: ['id' => int, 'vector' => float[], 'payload' => array].
     */
    public function upsert(array $points): void
    {
        if ($points === []) {
            return;
        }

        $this->request('PUT', '/collections/' . $this->collection() . '/points?wait=true', [
            'points' => array_values($points),
        ]);
    }

    public function deleteByDocument(int $documentId): void
    {
        $this->request('POST', '/collections/' . $this->collection() . '/points/delete?wait=true', [
            'filter' => ['must' => [['key' => 'document_id', 'match' => ['value' => $documentId]]]],
        ]);
    }

    public function deleteByModule(int $moduleId): void
    {
        $this->request('POST', '/collections/' . $this->collection() . '/points/delete?wait=true', [
            'filter' => ['must' => [['key' => 'module_id', 'match' => ['value' => $moduleId]]]],
        ]);
    }
    public function setActiveForModule(int $moduleId, bool $active): void
    {
        $this->request('POST', '/collections/' . $this->collection() . '/points/payload?wait=true', [
            'payload' => ['is_active' => $active],
            'filter' => ['must' => [['key' => 'module_id', 'match' => ['value' => $moduleId]]]],
        ]);
    }
    public function setActiveForDocument(int $documentId, bool $active): void
    {
        $this->request('POST', '/collections/' . $this->collection() . '/points/payload?wait=true', [
            'payload' => ['is_active' => $active],
            'filter' => ['must' => [['key' => 'document_id', 'match' => ['value' => $documentId]]]],
        ]);
    }

    /**
     * Cari titik terdekat. Hasil: daftar ['id', 'score', 'payload'].
     */
    public function search(array $vector, int $limit = 5, array $filter = []): array
    {
        $body = ['query' => $vector, 'limit' => $limit, 'with_payload' => true];

        if ($filter !== []) {
            $body['filter'] = $filter;
        }

        $result = $this->request('POST', '/collections/' . $this->collection() . '/points/query', $body);

        return $result['result']['points'] ?? [];
    }

    public function count(array $filter = []): int
    {
        $body = ['exact' => true];

        if ($filter !== []) {
            $body['filter'] = $filter;
        }

        $result = $this->request('POST', '/collections/' . $this->collection() . '/points/count', $body);

        return (int) ($result['result']['count'] ?? 0);
    }

    /**
     * Filter untuk pencarian: hanya chunk aktif, opsional dibatasi ke satu modul.
     */
    public static function activeFilter(?int $moduleId = null): array
    {
        $must = [['key' => 'is_active', 'match' => ['value' => true]]];

        if ($moduleId !== null) {
            $must[] = ['key' => 'module_id', 'match' => ['value' => $moduleId]];
        }

        return ['must' => $must];
    }

    private function request(string $method, string $path, array $body = []): array
    {
        $url = rtrim((string) config('qdrant.url'), '/') . $path;
        $http = Http::timeout(30)->acceptJson();

        if ($key = config('qdrant.api_key')) {
            $http = $http->withHeaders(['api-key' => $key]);
        }

        try {
            $response = $method === 'GET'
                ? $http->get($url)
                : $http->send($method, $url, ['json' => $body]);
        } catch (ConnectionException $e) {
            throw new RuntimeException('Tidak bisa terhubung ke Qdrant di ' . config('qdrant.url') . '. Pastikan Qdrant menyala.');
        }

        if (! $response->successful()) {
            $detail = $response->json('status.error') ?? $response->body();

            throw new RuntimeException('Qdrant error ' . $response->status() . ': ' . mb_substr((string) $detail, 0, 300));
        }

        return (array) $response->json();
    }
}
