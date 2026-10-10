<?php
require __DIR__ . '/web-admin/vendor/autoload.php';
$app = require_once __DIR__ . '/web-admin/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$vision = app(App\Services\ImageVisionService::class);
$images = App\Models\DocumentImage::where('description', '(Deskripsi gambar tidak tersedia)')->get();
$count = 0;
foreach ($images as $img) {
    if (!Illuminate\Support\Facades\Storage::exists($img->storage_path)) continue;
    $data = base64_encode(Illuminate\Support\Facades\Storage::get($img->storage_path));
    $desc = $vision->describe($data, $img->mime_type, (string)$img->section);
    if ($desc !== '(Deskripsi gambar tidak tersedia)') {
        $img->update(['description' => $desc]);
        $chunk = App\Models\DocumentChunk::where('document_id', $img->document_id)
            ->where('content', 'LIKE', '%[IMG_REF:' . $img->id . ']%')
            ->first();
        if ($chunk) {
            $chunk->update(['content' => "Lampiran Gambar: " . $desc . " [IMG_REF:" . $img->id . "]"]);
            app(App\Services\ChunkIndexer::class)->index($chunk->document);
        }
        echo "Fixed image " . $img->id . "\n";
        $count++;
    }
}
echo "Done. Fixed $count images.\n";
