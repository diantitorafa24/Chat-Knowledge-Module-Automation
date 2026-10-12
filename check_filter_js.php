<?php
$url = 'https://n8n-95zwwqkbwlat.jkt6.sumopod.my.id/api/v1/workflows/dTA9FUgcnKQFRajZ';
$apiKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI4NzczMjI2MS1kNjA0LTRkN2UtYjk5Ni0xOTkxMGFkZTNkNzYiLCJpc3MiOiJuOG4iLCJhdWQiOiJwdWJsaWMtYXBpIiwianRpIjoiYWU3ZDFhZjctMzViZS00ODgwLWEwYjYtODY2ZWRiYTk2YzBiIiwiaWF0IjoxNzkxODAxNDU2LCJleHAiOjE3OTE4MjQ0MDB9.4OLMZDuISqHqxJR7RkTSJQ_-UTEU3T1fagpm_EHR9rQ';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-N8n-Api-Key: ' . $apiKey]);
$res = curl_exec($ch);
curl_close($ch);

$wf = json_decode($res, true);
foreach ($wf['nodes'] as $n) {
    if ($n['name'] === 'Filter Grup') {
        echo "=== Filter Grup jsCode ===\n";
        echo $n['parameters']['jsCode'] . "\n";
    }
}
