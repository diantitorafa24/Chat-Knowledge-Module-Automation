<?php
$url = 'https://n8n-95zwwqkbwlat.jkt6.sumopod.my.id/api/v1/workflows/dTA9FUgcnKQFRajZ';
$apiKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI4NzczMjI2MS1kNjA0LTRkN2UtYjk5Ni0xOTkxMGFkZTNkNzYiLCJpc3MiOiJuOG4iLCJhdWQiOiJwdWJsaWMtYXBpIiwianRpIjoiYmI4Njg1YjItNDAxOC00NDRhLWFlYWEtNzkwMThiMjNiOWUxIiwiaWF0IjoxNzkxNjIzNDE1LCJleHAiOjE3OTE2NTE2MDB9.hMF_T_07tCyIWAiHhjkdpDOMI2f5_FhGmVqbjrb2_3U';

$jsonStr = file_get_contents('C:\xampp\htdocs\cika\n8n_current.json');
$wf = json_decode($jsonStr, true);

foreach ($wf['nodes'] as &$node) {
    if ($node['name'] === 'Format Jawaban Ditemukan') {
        foreach ($node['parameters']['assignments']['assignments'] as &$assignment) {
            if ($assignment['name'] === 'pesan_balasan') {
                $assignment['value'] = "={{ $('Tanya CIKA').first().json.answer }}";
            }
        }
    }
    if ($node['name'] === 'Send a text message') {
        if (isset($node['parameters']['inlineKeyboard']['rows'])) {
            foreach ($node['parameters']['inlineKeyboard']['rows'] as &$row) {
                foreach ($row['row']['buttons'] as &$btn) {
                    if (strpos($btn['text'], 'Membantu') !== false) {
                        $btn['text'] = '?? Membantu';
                    }
                    if (strpos($btn['text'], 'Kurang') !== false) {
                        $btn['text'] = '?? Kurang';
                    }
                }
            }
        }
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
// Ensure Unicode is preserved
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-N8n-Api-Key: ' . $apiKey,
    'Content-Type: application/json'
]);
$putResponse = curl_exec($ch);
curl_close($ch);
echo "Data mapped and emojis fixed properly!\n";
