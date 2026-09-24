<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Services\DocumentParserService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InspectDocx extends Command
{
    protected $signature = 'docx:inspect {document? : ID dokumen (default: dokumen terbaru)} {--limit=60 : Jumlah blok yang ditampilkan}';

    protected $description = 'Baca dokumen DOCX yang sudah diunggah dan tampilkan strukturnya';

    public function handle(DocumentParserService $parser): int
    {
        $document = $this->argument('document')
            ? Document::find($this->argument('document'))
            : Document::latest('id')->first();

        if (! $document) {
            $this->error('Dokumen tidak ditemukan.');

            return self::FAILURE;
        }

        if (! Storage::exists($document->file_path)) {
            $this->error('File dokumen tidak ada di penyimpanan.');

            return self::FAILURE;
        }

        $this->info("Dokumen #{$document->id}: {$document->original_filename}");

        try {
            $result = $parser->parse(Storage::path($document->file_path));
        } catch (\Throwable $e) {
            $this->error('Gagal membaca: ' . $e->getMessage());

            return self::FAILURE;
        }

        $s = $result['stats'];

        $this->line(sprintf(
            'Heading: %d | Paragraf: %d | List: %d | Tabel: %d | Karakter: %d',
            $s['headings'],
            $s['paragraphs'],
            $s['list_items'],
            $s['tables'],
            $s['characters']
        ));

        if ($s['headings'] === 0) {
            $this->warn('Tidak ada Heading terdeteksi. Chunking nanti akan kurang rapi. Gunakan style Heading 1 / Heading 2 di Word.');
        }

        $this->newLine();

        foreach (array_slice($result['blocks'], 0, (int) $this->option('limit')) as $block) {
            $this->line($this->format($block));
        }

        return self::SUCCESS;
    }

    private function format(array $block): string
    {
        return match ($block['type']) {
            'heading' => str_repeat('  ', max(0, $block['level'] - 1)) . "[H{$block['level']}] " . $block['text'],
            'list_item' => '    - ' . Str::limit($block['text'], 80),
            'table' => '    [tabel ' . count($block['rows']) . ' baris x ' . max(array_map('count', $block['rows'])) . ' kolom]',
            default => '    (p) ' . Str::limit($block['text'], 80),
        };
    }
}
