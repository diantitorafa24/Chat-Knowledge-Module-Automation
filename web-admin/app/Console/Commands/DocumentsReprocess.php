<?php

namespace App\Console\Commands;

use App\Jobs\ProcessDocumentJob;
use App\Models\Document;
use Illuminate\Console\Command;

class DocumentsReprocess extends Command
{
    protected $signature = 'documents:reprocess {document? : ID dokumen} {--all : Semua dokumen yang belum aktif}';

    protected $description = 'Masukkan dokumen ke antrean pemrosesan ulang (butuh pekerja antrean menyala)';

    public function handle(): int
    {
        $query = Document::query()->where('processing_status', '!=', 'active');

        if ($id = $this->argument('document')) {
            $query->whereKey($id);
        } elseif (! $this->option('all')) {
            $this->error('Isi ID dokumen, atau pakai --all.');

            return self::FAILURE;
        }

        $documents = $query->get();

        foreach ($documents as $document) {
            $document->update(['processing_status' => 'uploaded', 'progress' => 0, 'processing_error' => null]);
            ProcessDocumentJob::dispatch($document->id);
        }

        $this->info($documents->count() . ' dokumen dimasukkan ke antrean.');

        return self::SUCCESS;
    }
}
