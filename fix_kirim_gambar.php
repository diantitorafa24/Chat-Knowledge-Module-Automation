<?php
$url = 'https://n8n-95zwwqkbwlat.jkt6.sumopod.my.id/api/v1/workflows/dTA9FUgcnKQFRajZ';
$apiKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI4NzczMjI2MS1kNjA0LTRkN2UtYjk5Ni0xOTkxMGFkZTNkNzYiLCJpc3MiOiJuOG4iLCJhdWQiOiJwdWJsaWMtYXBpIiwianRpIjoiYmI4Njg1YjItNDAxOC00NDRhLWFlYWEtNzkwMThiMjNiOWUxIiwiaWF0IjoxNzkxNjIzNDE1LCJleHAiOjE3OTE2NTE2MDB9.hMF_T_07tCyIWAiHhjkdpDOMI2f5_FhGmVqbjrb2_3U';

$jsonStr = file_get_contents('C:\xampp\htdocs\cika\n8n_current.json');
$wf = json_decode($jsonStr, true);

foreach ($wf['nodes'] as &$node) {
    if ($node['name'] === 'Kirim Gambar') {
        // Fix credential - use correct Telegram CIKA-AI credential
        $node['credentials'] = [
            'telegramApi' => [
                'id' => 'NfTjLcnUwdxoXxMA',
                'name' => 'Telegram CIKA-AI'
            ]
        ];
        // Fix photo URL mapping and ensure not binary mode
        $node['parameters'] = [
            'operation' => 'sendPhoto',
            'chatId' => "={{ \$('Telegram Trigger').first().json.message.chat.id }}",
            'photo' => "={{ \$('Tanya CIKA').first().json.image_urls[0] }}",
            'additionalFields' => []
        ];
        $node['typeVersion'] = 1;
    }
}

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
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "HTTP: $httpCode\n";
echo substr($putResponse, 0, 200) . "\n";
