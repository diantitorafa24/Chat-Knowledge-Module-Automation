<?php
$json = json_decode(file_get_contents('C:\xampp\htdocs\cika\audit_n8n.json'), true);

echo "=== INSPECTING ALL NODES IN DETAIL ===\n";
foreach ($json['nodes'] as $n) {
    echo "\n>>> Node: {$n['name']} [{$n['type']}]\n";
    if (isset($n['parameters'])) {
        echo json_encode($n['parameters'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
    }
}
