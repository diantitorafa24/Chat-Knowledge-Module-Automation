<?php
$json = json_decode(file_get_contents('C:\xampp\htdocs\cika\audit_n8n.json'), true);
foreach ($json['nodes'] as $n) {
    if ($n['name'] === 'Send a text message') {
        echo json_encode($n, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
