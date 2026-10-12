<?php
$url = 'https://n8n-95zwwqkbwlat.jkt6.sumopod.my.id/api/v1/workflows/dTA9FUgcnKQFRajZ';
$apiKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI4NzczMjI2MS1kNjA0LTRkN2UtYjk5Ni0xOTkxMGFkZTNkNzYiLCJpc3MiOiJuOG4iLCJhdWQiOiJwdWJsaWMtYXBpIiwianRpIjoiYWU3ZDFhZjctMzViZS00ODgwLWEwYjYtODY2ZWRiYTk2YzBiIiwiaWF0IjoxNzkxODAxNDU2LCJleHAiOjE3OTE4MjQ0MDB9.4OLMZDuISqHqxJR7RkTSJQ_-UTEU3T1fagpm_EHR9rQ';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-N8n-Api-Key: ' . $apiKey]);
$res = curl_exec($ch);
curl_close($ch);

$wf = json_decode($res, true);
$newUrl = "https://optimal-records-diary-thousand.trycloudflare.com";

foreach ($wf['nodes'] as &$node) {
    if ($node['name'] === 'Tanya CIKA') {
        $node['parameters']['url'] = $newUrl . '/api/rag/ask';
    } elseif ($node['name'] === 'HTTP Request' && $node['type'] === 'n8n-nodes-base.httpRequest') {
        $node['parameters']['url'] = $newUrl . '/api/feedback';
    }
}
unset($node);

$payload = [
    'name' => $wf['name'],
    'nodes' => $wf['nodes'],
    'connections' => $wf['connections'],
    'settings' => $wf['settings']
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-N8n-Api-Key: ' . $apiKey,
    'Content-Type: application/json'
]);
$putResponse = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "PUT HTTP: $code\n";
echo ($code === 200) ? "Berhasil update URL Cloudflare di n8n!\n" : substr($putResponse, 0, 300) . "\n";
