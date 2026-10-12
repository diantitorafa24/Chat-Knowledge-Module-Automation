<?php
$json = json_decode(file_get_contents('C:\xampp\htdocs\cika\audit_n8n.json'), true);

foreach ($json['nodes'] as $n) {
    echo "========================================\n";
    echo "NODE: {$n['name']} ({$n['type']})\n";
    echo "========================================\n";
    if ($n['name'] === 'Telegram Trigger') {
        echo "Updates: " . json_encode($n['parameters']['updates'] ?? []) . "\n";
    } elseif ($n['name'] === 'If') {
        echo "Conditions: " . json_encode($n['parameters']['conditions'] ?? []) . "\n";
    } elseif ($n['name'] === 'Tanya CIKA') {
        echo "URL: " . ($n['parameters']['url'] ?? '') . "\n";
        echo "Method: " . ($n['parameters']['method'] ?? '') . "\n";
        echo "Body: " . json_encode($n['parameters']['bodyParameters'] ?? $n['parameters']['body'] ?? []) . "\n";
    } elseif ($n['name'] === 'Ada Jawaban?') {
        echo "Conditions: " . json_encode($n['parameters']['conditions'] ?? []) . "\n";
    } elseif ($n['name'] === 'Format Jawaban Ditemukan') {
        echo "Assignments: " . json_encode($n['parameters']['assignments'] ?? []) . "\n";
    } elseif ($n['name'] === 'Format Tidak Ditemukan') {
        echo "Assignments: " . json_encode($n['parameters']['assignments'] ?? []) . "\n";
    } elseif ($n['name'] === 'Send a text message') {
        echo "Text: " . ($n['parameters']['text'] ?? '') . "\n";
        echo "InlineKeyboard: " . json_encode($n['parameters']['inlineKeyboard'] ?? []) . "\n";
        echo "AdditionalFields: " . json_encode($n['parameters']['additionalFields'] ?? []) . "\n";
    } elseif ($n['name'] === 'Send a text message1') {
        echo "Text: " . ($n['parameters']['text'] ?? '') . "\n";
        echo "AdditionalFields: " . json_encode($n['parameters']['additionalFields'] ?? []) . "\n";
    } elseif ($n['name'] === 'HTTP Request') {
        echo "URL: " . ($n['parameters']['url'] ?? '') . "\n";
        echo "Method: " . ($n['parameters']['method'] ?? '') . "\n";
        echo "Body: " . json_encode($n['parameters']['bodyParameters'] ?? $n['parameters']['body'] ?? []) . "\n";
    } elseif ($n['name'] === 'Answer Query a callback') {
        echo "QueryId: " . ($n['parameters']['queryId'] ?? '') . "\n";
        echo "Text: " . ($n['parameters']['text'] ?? '') . "\n";
    }
}
