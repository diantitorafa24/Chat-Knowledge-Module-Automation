<?php
// Verifikasi: cek node list dan connections
$url = 'https://n8n-95zwwqkbwlat.jkt6.sumopod.my.id/api/v1/workflows/dTA9FUgcnKQFRajZ';
$apiKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI4NzczMjI2MS1kNjA0LTRkN2UtYjk5Ni0xOTkxMGFkZTNkNzYiLCJpc3MiOiJuOG4iLCJhdWQiOiJwdWJsaWMtYXBpIiwianRpIjoiYmI4Njg1YjItNDAxOC00NDRhLWFlYWEtNzkwMThiMjNiOWUxIiwiaWF0IjoxNzkxNjIzNDE1LCJleHAiOjE3OTE2NTE2MDB9.hMF_T_07tCyIWAiHhjkdpDOMI2f5_FhGmVqbjrb2_3U';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-N8n-Api-Key: ' . $apiKey]);
$wf = json_decode(curl_exec($ch), true);
curl_close($ch);

echo "=== NODE LIST ===\n";
foreach ($wf['nodes'] as $n) { echo "- " . $n['name'] . "\n"; }

echo "\n=== CONNECTIONS (Alur Utama) ===\n";
$flow = ['Telegram Trigger', 'Filter Grup', 'If', 'Tanya CIKA', 'Ada Jawaban?'];
foreach ($flow as $nodeName) {
    if (isset($wf['connections'][$nodeName]['main'][0][0])) {
        $next = $wf['connections'][$nodeName]['main'][0][0]['node'];
        echo "$nodeName ? $next\n";
    }
}
