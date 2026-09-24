<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\Module;
use App\Services\DocumentParserService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class ProcessDocumentJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 300;

    public function __construct(public int $documentId)
    {
    }

    public function handle(DocumentParserService $parser): void
    {
        $document = Document::find($this->documentId);

        if (! $document) {
            return;
        }

        $this->setModuleStatus($document->module_id, 'processing');
        $document->update(['processing_status' => 'extracting', 'progress' => 10, 'processing_error' => null]);

        try {
            if (! Storage::exists($document->file_path)) {
                throw new RuntimeException('File dokumen tidak ditemukan di penyimpanan.');
            }

            $result = $parser->parse(Storage::path($document->file_path));

            Storage::put(
                self::extractedPath($document),
                json_encode([
                    'document_id' => $document->id,
                    'module_id' => $document->module_id,
                    'stats' => $result['stats'],
                    'blocks' => $result['blocks'],
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)
            );

            $document->update(['processing_status' => 'extracted', 'progress' => 30]);
        } catch (Throwable $e) {
            $this->markFailed($document, $e);
        }
    }

    public function failed(?Throwable $exception): void
    {
        $document = Document::find($this->documentId);

        if ($document && $exception) {
            $this->markFailed($document, $exception);
        }
    }

    public static function extractedPath(Document $document): string
    {
        return 'modules/' . $document->module_id . '/extracted/' . $document->id . '.json';
    }

    private function markFailed(Document $document, Throwable $e): void
    {
        Log::error('Proses dokumen gagal', ['document_id' => $document->id, 'error' => $e->getMessage()]);

        $document->update([
            'processing_status' => 'failed',
            'processing_error' => mb_substr($e->getMessage(), 0, 500),
        ]);

        $this->setModuleStatus($document->module_id, 'failed');
    }

    private function setModuleStatus(int $moduleId, string $status): void
    {
        Module::where('id', $moduleId)->where('status', '!=', 'active')->update(['status' => $status]);
    }
}
