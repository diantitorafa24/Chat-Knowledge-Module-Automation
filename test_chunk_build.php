<?php
require __DIR__ . '/web-admin/vendor/autoload.php';
$app = require_once __DIR__ . '/web-admin/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Document;
use App\Services\DocumentParserService;
use App\Services\ChunkingService;
use Illuminate\Support\Facades\Storage;

$doc = Document::find(3);
$path = Storage::path($doc->file_path);
$parser = new DocumentParserService();
$res = $parser->parse($path);

$chunker = new ChunkingService();
$chunks = $chunker->buildChunks($res['blocks']);
echo "Total chunks generated: " . count($chunks) . "\n";

foreach ($chunks as $idx => $c) {
    if (strpos($c['content'], '172.31') !== false || strpos($c['content'], 'Remote Mikrotik') !== false || strpos($c['content'], 'Daftar IP') !== false) {
        echo "=== CHUNK $idx ===\n";
        echo "Title: " . $c['title'] . "\n";
        echo "Section: " . $c['section'] . "\n";
        echo "Subsection: " . $c['subsection'] . "\n";
        echo "Content: \n" . $c['content'] . "\n\n";
    }
}
