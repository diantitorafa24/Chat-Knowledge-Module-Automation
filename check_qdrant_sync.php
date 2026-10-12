<?php
require __DIR__ . '/web-admin/vendor/autoload.php';
$app = require_once __DIR__ . '/web-admin/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\QdrantService;

$qdrant = app(QdrantService::class);
$totalPoints = $qdrant->count();
$activePoints = $qdrant->count(QdrantService::activeFilter());

echo "=== QDRANT HEALTH CHECK ===\n";
echo "Total Points in Qdrant: $totalPoints\n";
echo "Active Points in Qdrant: $activePoints\n";

$mysqlTotal = \App\Models\DocumentChunk::count();
$mysqlDoc3 = \App\Models\DocumentChunk::where('document_id', 3)->count();
echo "MySQL Total Chunks: $mysqlTotal\n";
echo "MySQL Doc 3 Chunks: $mysqlDoc3\n";

if ($activePoints === $mysqlDoc3) {
    echo "PERFECT SYNC! All active chunks in Document 3 match Qdrant active points.\n";
} else {
    echo "Difference found: Active in Qdrant ($activePoints) vs MySQL Doc 3 ($mysqlDoc3)\n";
}
