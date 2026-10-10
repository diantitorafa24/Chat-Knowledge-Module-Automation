<?php
require __DIR__ . '/web-admin/vendor/autoload.php';
$app = require_once __DIR__ . '/web-admin/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$settings = \App\Models\Setting::all();
foreach ($settings as $s) {
    if (stripos($s->value, 'Halo') !== false || stripos($s->key, 'greeting') !== false) {
        echo $s->key . " = " . $s->value . "\n";
    }
}
