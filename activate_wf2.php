<?php
$url = 'https://n8n-95zwwqkbwlat.jkt6.sumopod.my.id/api/v1/workflows/dTA9FUgcnKQFRajZ/activate';
$apiKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI4NzczMjI2MS1kNjA0LTRkN2UtYjk5Ni0xOTkxMGFkZTNkNzYiLCJpc3MiOiJuOG4iLCJhdWQiOiJwdWJsaWMtYXBpIiwianRpIjoiYWU3ZDFhZjctMzViZS00ODgwLWEwYjYtODY2ZWRiYTk2YzBiIiwiaWF0IjoxNzkxODAxNDU2LCJleHAiOjE3OTE4MjQ0MDB9.4OLMZDuISqHqxJR7RkTSJQ_-UTEU3T1fagpm_EHR9rQ';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, "{}");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-N8n-Api-Key: ' . $apiKey,
    'Content-Type: application/json'
]);
$res = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Activate HTTP: $code\n";
echo substr($res, 0, 200) . "\n";
