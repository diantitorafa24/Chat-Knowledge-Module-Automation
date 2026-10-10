import os

path = r"C:\xampp\htdocs\cika\web-admin\routes\web.php"
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Add temp route to fix images
temp_route = """
Route::get('/fix-images', function (\\App\\Services\\ImageVisionService $vision) {
    $images = \\App\\Models\\DocumentImage::where('description', '(Deskripsi gambar tidak tersedia)')->get();
    $count = 0;
    foreach ($images as $img) {
        if (!\\Illuminate\\Support\\Facades\\Storage::exists($img->storage_path)) continue;
        
        $data = base64_encode(\\Illuminate\\Support\\Facades\\Storage::get($img->storage_path));
        $desc = $vision->describe($data, $img->mime_type, $img->section);
        
        if ($desc !== '(Deskripsi gambar tidak tersedia)') {
            $img->update(['description' => $desc]);
            
            // Update the chunk text as well!
            $chunk = \\App\\Models\\DocumentChunk::where('document_id', $img->document_id)
                ->where('text', 'LIKE', '%[IMG_REF:' . $img->id . ']%')
                ->first();
                
            if ($chunk) {
                $chunk->update(['text' => "Lampiran Gambar: " . $desc . " [IMG_REF:" . $img->id . "]"]);
                // update qdrant point
                app(\\App\\Services\\ChunkIndexer::class)->index($chunk->document);
            }
            $count++;
        }
    }
    return "Fixed $count images";
});
"""

if "fix-images" not in content:
    with open(path, 'a', encoding='utf-8') as f:
        f.write(temp_route)
print("Route added.")
