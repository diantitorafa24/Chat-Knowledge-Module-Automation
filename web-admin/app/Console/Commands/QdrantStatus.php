<?php

namespace App\Console\Commands;

use App\Models\DocumentChunk;
use App\Services\QdrantService;
use Illuminate\Console\Command;

class QdrantStatus extends Command
{
    protected $signature = 'qdrant:status';

    protected $description = 'Tampilkan jumlah titik di Qdrant dibandingkan jumlah chunk di MySQL';

    public function handle(QdrantService $qdrant): int
    {
        try {
            if (! $qdrant->collectionExists()) {
                $this->warn('Koleksi ' . $qdrant->collection() . ' belum ada. Jalankan: php artisan qdrant:setup');

                return self::FAILURE;
            }

            $this->line('Koleksi              : ' . $qdrant->collection());
            $this->line('Titik di Qdrant      : ' . $qdrant->count());
            $this->line('Titik aktif          : ' . $qdrant->count(QdrantService::activeFilter()));
            $this->line('Chunk di MySQL       : ' . DocumentChunk::count());
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
