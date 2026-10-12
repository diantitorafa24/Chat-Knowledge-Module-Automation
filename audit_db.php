<?php
require __DIR__ . '/web-admin/vendor/autoload.php';
$app = require_once __DIR__ . '/web-admin/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== FAILED JOBS ===\n";
$failedJobs = \Illuminate\Support\Facades\DB::table('failed_jobs')->get();
echo "Count: " . count($failedJobs) . "\n";
foreach ($failedJobs as $fj) {
    echo "ID: {$fj->id} | Queue: {$fj->queue} | Failed at: {$fj->failed_at}\n";
    echo "Exception: " . substr($fj->exception, 0, 200) . "...\n";
}

echo "\n=== FEEDBACK TABLE ===\n";
$feedbacks = \Illuminate\Support\Facades\DB::table('feedbacks')->get();
echo "Count: " . count($feedbacks) . "\n";
foreach ($feedbacks as $fb) {
    echo "ID: {$fb->id} | ChatLogID: {$fb->chat_log_id} | Rating: {$fb->rating} | Comment: {$fb->comment} | Created: {$fb->created_at}\n";
}

echo "\n=== CHAT LOGS STATS ===\n";
$chatLogsCount = \Illuminate\Support\Facades\DB::table('chat_logs')->count();
$answeredCount = \Illuminate\Support\Facades\DB::table('chat_logs')->where('is_answered', true)->count();
$unansweredCount = \Illuminate\Support\Facades\DB::table('chat_logs')->where('is_answered', false)->count();
echo "Total: $chatLogsCount | Answered: $answeredCount | Unanswered: $unansweredCount\n";
