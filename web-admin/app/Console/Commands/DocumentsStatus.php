<?php

namespace App\Console\Commands;

use App\Models\Document;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class DocumentsStatus extends Command
{
    protected $signature = 'documents:status';

    protected $description = 'Tampilkan status pemrosesan semua dokumen';

    public function handle(): int
    {
        $rows = Document::with('module')
            ->orderBy('id')
            ->get()
            ->map(fn ($d) => [
                $d->id,
                $d->module->code ?? '-',
                $d->original_filename,
                $d->processing_status,
                $d->progress . '%',
                Str::limit((string) $d->processing_error, 70),
            ])
            ->all();

        $this->table(['ID', 'Modul', 'File', 'Status', 'Progress', 'Error'], $rows);

        return self::SUCCESS;
    }
}
