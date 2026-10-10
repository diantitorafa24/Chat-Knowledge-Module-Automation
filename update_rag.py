import os

path = r"C:\xampp\htdocs\cika\web-admin\app\Services\RagSearchService.php"
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Modify search() to extract IMG_REFs and return image URLs
extract_logic = """
        $imageUrls = [];
        foreach ($results as $hit) {
            $chunkId = $hit['id'];
            $chunk = $chunkMap[$chunkId] ?? null;

            if (! $chunk) {
                continue;
            }
            
            // Ekstrak gambar referensi
            if (preg_match_all('/\[IMG_REF:(\d+)\]/', $chunk->text, $matches)) {
                foreach ($matches[1] as $imgId) {
                    $img = \\App\\Models\\DocumentImage::find($imgId);
                    if ($img && $img->is_active) {
                        // Tambahkan URL pubik ke array (hapus prefix localhost jika perlu, tapi sementara pakai path)
                        $imageUrls[] = config('app.url') . $img->publicUrl();
                    }
                }
            }
            
            // Bersihkan tag IMG_REF dari teks sebelum diberikan ke LLM
            $cleanText = preg_replace('/\[IMG_REF:\d+\]/', '', $chunk->text);

            $formatted[] = [
                'module_code' => $chunk->document->module->code ?? '-',
                'module_name' => $chunk->document->module->name ?? '-',
                'version' => $chunk->document->module->active_version ?? '-',
                'section' => $chunk->heading,
                'subsection' => $chunk->subheading,
                'text' => trim($cleanText),
                'score' => round($hit['score'], 2),
            ];
            
            $contextParts[] = sprintf(
                "Sumber: %s (%s)\nBagian: %s\nTeks:\n%s",
                $chunk->document->module->name ?? '-',
                $chunk->document->module->code ?? '-',
                $chunk->heading . ($chunk->subheading ? ' > ' . $chunk->subheading : ''),
                trim($cleanText)
            );
        }

        return [
            'question' => $question,
            'found' => true,
            'context' => implode("\n\n---\n\n", $contextParts),
            'results' => $formatted,
            'image_urls' => array_values(array_unique($imageUrls)),
        ];
"""
# Replace the formatting block
content = content.replace("""        foreach ($results as $hit) {
            $chunkId = $hit['id'];
            $chunk = $chunkMap[$chunkId] ?? null;

            if (! $chunk) {
                continue;
            }

            $formatted[] = [
                'module_code' => $chunk->document->module->code ?? '-',
                'module_name' => $chunk->document->module->name ?? '-',
                'version' => $chunk->document->module->active_version ?? '-',
                'section' => $chunk->heading,
                'subsection' => $chunk->subheading,
                'text' => $chunk->text,
                'score' => round($hit['score'], 2),
            ];

            $contextParts[] = sprintf(
                "Sumber: %s (%s)\nBagian: %s\nTeks:\n%s",
                $chunk->document->module->name ?? '-',
                $chunk->document->module->code ?? '-',
                $chunk->heading . ($chunk->subheading ? ' > ' . $chunk->subheading : ''),
                $chunk->text
            );
        }

        return [
            'question' => $question,
            'found' => true,
            'context' => implode("\n\n---\n\n", $contextParts),
            'results' => $formatted,
        ];""", extract_logic)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("RagSearchService updated.")
