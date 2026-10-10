<?php
$url = 'https://n8n-95zwwqkbwlat.jkt6.sumopod.my.id/api/v1/workflows/dTA9FUgcnKQFRajZ';
$apiKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI4NzczMjI2MS1kNjA0LTRkN2UtYjk5Ni0xOTkxMGFkZTNkNzYiLCJpc3MiOiJuOG4iLCJhdWQiOiJwdWJsaWMtYXBpIiwianRpIjoiYmI4Njg1YjItNDAxOC00NDRhLWFlYWEtNzkwMThiMjNiOWUxIiwiaWF0IjoxNzkxNjIzNDE1LCJleHAiOjE3OTE2NTE2MDB9.hMF_T_07tCyIWAiHhjkdpDOMI2f5_FhGmVqbjrb2_3U';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-N8n-Api-Key: ' . $apiKey]);
$wf = json_decode(curl_exec($ch), true);
curl_close($ch);

foreach ($wf['nodes'] as &$node) {
    if ($node['name'] === 'If') {
        // Ganti kondisi: pesan ada DAN (chat private ATAU ada mention bot)
        $node['parameters']['conditions']['conditions'] = [
            [
                'id' => 'cond-msg-exists',
                'leftValue' => '={{ $json.message }}',
                'rightValue' => '',
                'operator' => ['type' => 'object', 'operation' => 'exists', 'singleValue' => true]
            ],
            [
                'id' => 'cond-private-or-mention',
                // true jika chat private, ATAU pesan mengandung mention (@username)
                'leftValue' => '={{ $json.message.chat.type === "private" || ($json.message.entities || []).some(e => e.type === "mention") || ($json.message.caption_entities || []).some(e => e.type === "mention") }}',
                'rightValue' => true,
                'operator' => ['type' => 'boolean', 'operation' => 'true', 'singleValue' => true]
            ]
        ];
        $node['parameters']['conditions']['combinator'] = 'and';
    }
    
    // Juga update Tanya CIKA - hapus @botname dari teks pertanyaan
    if ($node['name'] === 'Tanya CIKA') {
        $node['parameters']['bodyParameters']['parameters'][0]['value'] = 
            "={{ $json.message.text.replace(/@\w+\s*/g, '').trim() }}";
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
echo ($code === 200) ? "Berhasil! CIKA sekarang hanya respon ketika di-tag di grup.\n" : substr($res, 0, 400) . "\n";
