<?php
// Check Laravel logs for recent errors
$logFile = 'C:\xampp\htdocs\cika\web-admin\storage\logs\laravel.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $lastLines = array_slice($lines, -150);
    echo "=== RECENT LARAVEL LOG ENTRIES ===\n";
    echo implode("", $lastLines);
} else {
    echo "No log file found.\n";
}
