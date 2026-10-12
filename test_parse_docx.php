<?php
require __DIR__ . '/web-admin/vendor/autoload.php';
$app = require_once __DIR__ . '/web-admin/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Document;
use App\Services\DocumentParserService;
use Illuminate\Support\Facades\Storage;

$doc = Document::find(3);
$path = Storage::path($doc->file_path);
echo "File path: $path\n";
echo "File exists: " . (file_exists($path) ? "YES" : "NO") . "\n";

$parser = new DocumentParserService();
$res = $parser->parse($path);
echo "Total blocks: " . count($res['blocks']) . "\n";

$foundIp = false;
foreach ($res['blocks'] as $idx => $b) {
    $text = $b['text'] ?? ($b['type'] === 'table' ? json_encode($b['rows']) : '');
    if (strpos($text, '172.31') !== false || strpos($text, 'Remote Mikrotik') !== false || strpos($text, 'Daftar IP') !== false) {
        echo "Block $idx [{$b['type']}]: " . substr($text, 0, 200) . "...\n";
        $foundIp = true;
    }
}
if (!$foundIp) {
    echo "TIDAK DITEMUKAN teks 172.31 / Daftar IP / Remote Mikrotik sama sekali dalam blocks!\n";
}
