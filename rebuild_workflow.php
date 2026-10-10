<?php
$url = 'https://n8n-95zwwqkbwlat.jkt6.sumopod.my.id/api/v1/workflows/dTA9FUgcnKQFRajZ';
$apiKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI4NzczMjI2MS1kNjA0LTRkN2UtYjk5Ni0xOTkxMGFkZTNkNzYiLCJpc3MiOiJuOG4iLCJhdWQiOiJwdWJsaWMtYXBpIiwianRpIjoiYmI4Njg1YjItNDAxOC00NDRhLWFlYWEtNzkwMThiMjNiOWUxIiwiaWF0IjoxNzkxNjIzNDE1LCJleHAiOjE3OTE2NTE2MDB9.hMF_T_07tCyIWAiHhjkdpDOMI2f5_FhGmVqbjrb2_3U';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-N8n-Api-Key: ' . $apiKey]);
$res = curl_exec($ch);
curl_close($ch);
$wf = json_decode($res, true);

// 1. Remove Kirim Gambar and Cek Gambar nodes
$nodesToRemove = ['Kirim Gambar', 'Cek Gambar'];
$wf['nodes'] = array_values(array_filter($wf['nodes'], function($node) use ($nodesToRemove) {
    return !in_array($node['name'], $nodesToRemove);
}));

// 2. Fix connections: Ada Jawaban? true -> Format Jawaban Ditemukan (instead of Cek Gambar)
$wf['connections']['Ada Jawaban?']['main'][0] = [
    ['node' => 'Format Jawaban Ditemukan', 'type' => 'main', 'index' => 0]
];

// Remove leftover Cek Gambar and Kirim Gambar connections
unset($wf['connections']['Cek Gambar']);
unset($wf['connections']['Kirim Gambar']);

// 3. Fix Format Jawaban Ditemukan to map answer correctly
foreach ($wf['nodes'] as &$node) {
    if ($node['name'] === 'Format Jawaban Ditemukan') {
        $node['parameters']['assignments']['assignments'] = [
            ['id' => 'a1', 'name' => 'pesan_balasan', 'type' => 'string', 'value' => "={{ \$json.answer }}"],
            ['id' => 'a3', 'name' => 'chat_log_id', 'type' => 'number', 'value' => "={{ \$json.chat_log_id }}"],
        ];
        $node['parameters']['includeOtherFields'] = false;
    }
    // 4. Fix emoji buttons - use actual unicode emoji characters
    if ($node['name'] === 'Send a text message') {
        $node['parameters']['inlineKeyboard']['rows'] = [[
            'row' => [
                'buttons' => [
                    [
                        'text' => "\u{1F44D} Membantu",
                        'additionalFields' => ['callback_data' => '=fb_1_{{ $json.chat_log_id }}']
                    ],
                    [
                        'text' => "\u{1F44E} Kurang",
                        'additionalFields' => ['callback_data' => '=fb_0_{{ $json.chat_log_id }}']
                    ]
                ]
            ]
        ]];
    }
}
unset($node);

$payload = [
    'name' => $wf['name'],
    'nodes' => $wf['nodes'],
    'connections' => $wf['connections'],
    'settings' => $wf['settings']
];

$json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
file_put_contents('C:\xampp\htdocs\cika\debug_payload.json', $json);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-N8n-Api-Key: ' . $apiKey,
    'Content-Type: application/json'
]);
$putResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "HTTP: $httpCode\n";
if ($httpCode !== 200) echo substr($putResponse, 0, 300) . "\n";
else echo "Sukses! Node foto dihapus, emoji diperbaiki.\n";
