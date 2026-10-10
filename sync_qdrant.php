<?php
require __DIR__ . '/web-admin/vendor/autoload.php';
$app = require_once __DIR__ . '/web-admin/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Fix Qdrant sync
$qdrant = app(App\Services\QdrantService::class);
$qdrant->setActiveForDocument(3, true);

echo "Qdrant is_active di-sync kembali ke true untuk dokumen 3.\n";
