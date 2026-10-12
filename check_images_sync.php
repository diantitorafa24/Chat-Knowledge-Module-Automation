<?php
require __DIR__ . '/web-admin/vendor/autoload.php';
$app = require_once __DIR__ . '/web-admin/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$imagesCount = \App\Models\DocumentImage::count();
$activeImages = \App\Models\DocumentImage::where('is_active', true)->count();
echo "=== DOCUMENT IMAGES CHECK ===\n";
echo "Total Images: $imagesCount\n";
echo "Active Images: $activeImages\n";

$missingFiles = 0;
foreach (\App\Models\DocumentImage::all() as $img) {
    if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($img->storage_path)) {
        $missingFiles++;
    }
}
echo "Missing image files in public storage: $missingFiles\n";
