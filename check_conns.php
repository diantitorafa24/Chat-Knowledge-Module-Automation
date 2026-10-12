<?php
$json = json_decode(file_get_contents('C:\xampp\htdocs\cika\audit_n8n.json'), true);
echo "=== CONNECTIONS ===\n";
echo json_encode($json['connections'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
