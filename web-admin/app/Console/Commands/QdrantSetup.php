<?php

namespace App\Console\Commands;

use App\Services\QdrantService;
use Illuminate\Console\Command;

class QdrantSetup extends Command
{
    protected $signature = 'qdrant:setup';

    protected $description = 'Buat koleksi Qdrant untuk chunk (aman dijalankan berulang)';

    public function handle(QdrantService $qdrant): int
    {
        try {
            $created = $qdrant->ensureCollection();

            $this->info(($created ? 'Koleksi dibuat: ' : 'Koleksi sudah ada: ') . $qdrant->collection());
            $this->line('Dimensi: ' . config('embedding.dimensions') . ' | Jarak: Cosine | Jumlah titik: ' . $qdrant->count());
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
