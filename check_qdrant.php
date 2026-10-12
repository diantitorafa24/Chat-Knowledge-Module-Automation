<?php
require __DIR__ . '/web-admin/vendor/autoload.php';
$app = require_once __DIR__ . '/web-admin/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$qdrant = app(\App\Services\QdrantService::class);
$info = $qdrant->getCollectionInfo();
echo "=== QDRANT COLLECTION INFO ===\n";
echo "Status: " . ($info['status'] ?? 'unknown') . "\n";
echo "Vectors count: " . ($info['vectors_count'] ?? 0) . "\n";
echo "Indexed vectors count: " . ($info['indexed_vectors_count'] ?? 0) . "\n";
echo "Points count: " . ($info['points_count'] ?? 0) . "\n";

// Count how many active points exist for document 3
$active = \App\Models\DocumentChunk::where('document_id', 3)->count();
echo "MySQL Document 3 chunks: $active\n";
