import os

path = r"C:\xampp\htdocs\cika\web-admin\app\Jobs\ProcessDocumentJob.php"
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Add ImageVisionService and DocumentImage to uses
content = content.replace("use App\Models\Module;", "use App\Models\Module;\nuse App\Models\DocumentImage;\nuse App\Services\ImageVisionService;")

# Inject ImageVisionService in handle()
content = content.replace("public function handle(DocumentParserService $parser, ChunkingService $chunker, ChunkIndexer $indexer): void", "public function handle(DocumentParserService $parser, ChunkingService $chunker, ChunkIndexer $indexer, ImageVisionService $vision): void")

process_images = """
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
"""

content = content.replace("            Storage::put(\n                self::extractedPath($document),", process_images + "                self::extractedPath($document),")

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("ProcessDocumentJob updated.")
