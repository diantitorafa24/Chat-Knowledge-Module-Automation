<?php
try {
    $l = \App\Models\ChatLog::create([
        'question' => 'tes',
        'answer' => 'tes jawaban',
        'is_answered' => true,
    ]);
    echo "BERHASIL ID: " . $l->id . PHP_EOL;
} catch (\Throwable $e) {
    echo "GAGAL: " . $e->getMessage() . PHP_EOL;
}
