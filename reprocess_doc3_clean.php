<?php
require __DIR__ . '/web-admin/vendor/autoload.php';
$app = require_once __DIR__ . '/web-admin/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Document;
use App\Models\DocumentImage;
use App\Services\DocumentParserService;
use App\Services\ChunkingService;
use App\Services\ChunkIndexer;
use App\Services\QdrantService;
use Illuminate\Support\Facades\Storage;

$document = Document::find(3);
echo "Processing document {$document->id} ({$document->original_filename})...\n";

$parser = app(DocumentParserService::class);
$chunker = app(ChunkingService::class);
$indexer = app(ChunkIndexer::class);
$qdrant = app(QdrantService::class);

$result = $parser->parse(Storage::path($document->file_path));
echo "Parsed " . count($result['blocks']) . " blocks.\n";

// Map existing DocumentImage records into blocks so we don't need to re-call Gemini Vision
$images = DocumentImage::where('document_id', $document->id)->orderBy('image_index')->get()->keyBy('image_index');

$imageIndex = 1;
foreach ($result['blocks'] as &$block) {
    if ($block['type'] === 'image') {
        $docImage = $images->get($imageIndex);
        $desc = $docImage?->description ?? '(Deskripsi gambar tidak tersedia)';
        $imgId = $docImage?->id ?? $imageIndex;
        
        $block = [
            'type' => 'paragraph',
            'text' => "Lampiran Gambar: " . $desc . " [IMG_REF:{$imgId}]",
        ];
        $imageIndex++;
    }
}
unset($block);

echo "Total images mapped: " . ($imageIndex - 1) . "\n";

// Chunking
$chunkCount = $chunker->chunk($document, $result['blocks']);
echo "Generated $chunkCount clean chunks in MySQL.\n";

// Indexing to Qdrant
echo "Indexing chunks to Qdrant...\n";
$indexer->index($document);

// Ensure is_active in Qdrant is TRUE
$qdrant->setActiveForDocument($document->id, true);
DocumentImage::where('document_id', $document->id)->update(['is_active' => true]);

echo "SUCCESS! Document 3 completely re-chunked and re-indexed with all text preserved!\n";
