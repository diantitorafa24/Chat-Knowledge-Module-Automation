<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\Module;
use App\Models\DocumentImage;
use App\Services\ImageVisionService;
use App\Services\ChunkIndexer;
use App\Services\ChunkingService;
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

    public int $timeout = 1200;

    public function __construct(public int $documentId)
    {
    }

    public function handle(DocumentParserService $parser, ChunkingService $chunker, ChunkIndexer $indexer, ImageVisionService $vision): void
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


            // Proses gambar dengan Gemini Vision
            $document->update(['processing_status' => 'processing_images']);
            $currentSection = '';
            $imageIndex = 1;
            
            foreach ($result['blocks'] as &$block) {
                if ($block['type'] === 'heading') {
                    $currentSection = $block['text'];
                }
                
                if ($block['type'] === 'image') {
                    // Simpan gambar ke storage
                    $ext = str_replace('image/', '', $block['mime']);
                    if ($ext === 'jpeg') $ext = 'jpg';
                    $fileName = 'modules/' . $document->module_id . '/images/doc_' . $document->id . '_img_' . $imageIndex . '.' . $ext;
                    Storage::put($fileName, base64_decode($block['data']));
                    
                    // Panggil Gemini Vision
                    $desc = $vision->describe($block['data'], $block['mime'], $currentSection);
                    
                    // Simpan ke database
                    $docImage = DocumentImage::create([
                        'document_id' => $document->id,
                        'module_id' => $document->module_id,
                        'image_index' => $imageIndex,
                        'storage_path' => $fileName,
                        'mime_type' => $block['mime'],
                        'description' => $desc,
                        'section' => $currentSection,
                        'is_active' => false,
                    ]);
                    
                    // Ubah block menjadi paragraf agar di-chunk oleh ChunkingService
                    $block = [
                        'type' => 'paragraph',
                        'text' => "Lampiran Gambar: " . $desc . " [IMG_REF:{$docImage->id}]",
                    ];
                    
                    $imageIndex++;
                }
            }
            unset($block);

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

            $document->update(['processing_status' => 'chunking', 'progress' => 40]);
            $chunker->chunk($document, $result['blocks']);
            $document->update(['processing_status' => 'chunked', 'progress' => 60]);

            $document->update(['processing_status' => 'embedding', 'progress' => 65]);
            $indexer->index($document, function (int $done, int $total) use ($document) {
                $document->update(['progress' => 65 + (int) floor($done / $total * 25)]);
            });
            $document->update(['processing_status' => 'indexed', 'progress' => 90]);

            $this->setModuleStatus($document->module_id, 'inactive');
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
