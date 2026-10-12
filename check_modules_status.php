<?php
require __DIR__ . '/web-admin/vendor/autoload.php';
$app = require_once __DIR__ . '/web-admin/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$modules = \App\Models\Module::with('versions')->get();
foreach ($modules as $m) {
    echo "Module: {$m->code} - {$m->name} | Status: {$m->status} | Current Version: {$m->current_version}\n";
    foreach ($m->versions as $v) {
        echo "   -> Version {$v->version} | Status: {$v->status} | Doc ID: {$v->document_id}\n";
    }
}
