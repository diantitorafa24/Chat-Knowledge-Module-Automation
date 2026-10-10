<?php
$url = 'https://n8n-95zwwqkbwlat.jkt6.sumopod.my.id/api/v1/workflows/dTA9FUgcnKQFRajZ';
$apiKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI4NzczMjI2MS1kNjA0LTRkN2UtYjk5Ni0xOTkxMGFkZTNkNzYiLCJpc3MiOiJuOG4iLCJhdWQiOiJwdWJsaWMtYXBpIiwianRpIjoiYmI4Njg1YjItNDAxOC00NDRhLWFlYWEtNzkwMThiMjNiOWUxIiwiaWF0IjoxNzkxNjIzNDE1LCJleHAiOjE3OTE2NTE2MDB9.hMF_T_07tCyIWAiHhjkdpDOMI2f5_FhGmVqbjrb2_3U';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-N8n-Api-Key: ' . $apiKey]);
$wf = json_decode(curl_exec($ch), true);
curl_close($ch);

// Fix Tanya CIKA - kembalikan ke ekspresi yang valid
foreach ($wf['nodes'] as &$node) {
    if ($node['name'] === 'Tanya CIKA') {
        foreach ($node['parameters']['bodyParameters']['parameters'] as &$param) {
            if ($param['name'] === 'question') {
                // Ekspresi valid untuk n8n - pakai replace sederhana tanpa /g flag
                $param['value'] = "={{ $json.message.text.replace(/@\w+\s*/, '').trim() }}";
            }
        }
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
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-N8n-Api-Key: ' . $apiKey, 'Content-Type: application/json']);
$res = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "HTTP: $code\n";

// Verifikasi langsung
if ($code === 200) {
    foreach (json_decode($res, true)['nodes'] as $n) {
        if ($n['name'] === 'Tanya CIKA') {
            foreach ($n['parameters']['bodyParameters']['parameters'] as $p) {
                if ($p['name'] === 'question') echo "question value: " . $p['value'] . "\n";
            }
        }
    }
}
