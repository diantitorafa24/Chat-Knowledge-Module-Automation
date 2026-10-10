<?php
$url = 'https://n8n-95zwwqkbwlat.jkt6.sumopod.my.id/api/v1/workflows/dTA9FUgcnKQFRajZ';
$apiKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI4NzczMjI2MS1kNjA0LTRkN2UtYjk5Ni0xOTkxMGFkZTNkNzYiLCJpc3MiOiJuOG4iLCJhdWQiOiJwdWJsaWMtYXBpIiwianRpIjoiYmI4Njg1YjItNDAxOC00NDRhLWFlYWEtNzkwMThiMjNiOWUxIiwiaWF0IjoxNzkxNjIzNDE1LCJleHAiOjE3OTE2NTE2MDB9.hMF_T_07tCyIWAiHhjkdpDOMI2f5_FhGmVqbjrb2_3U';

$jsonStr = file_get_contents('C:\xampp\htdocs\cika\n8n_current.json');
$wf = json_decode($jsonStr, true);

foreach ($wf['nodes'] as &$node) {
    if ($node['name'] === 'Send a text message') {
        if (isset($node['parameters']['inlineKeyboard']['rows'])) {
            foreach ($node['parameters']['inlineKeyboard']['rows'] as &$row) {
                foreach ($row['row']['buttons'] as &$btn) {
                    $btn['text'] = str_replace('?? Membantu', '?? Membantu', $btn['text']);
                    $btn['text'] = str_replace('?? Kurang', '?? Kurang', $btn['text']);
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
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-N8n-Api-Key: ' . $apiKey,
    'Content-Type: application/json'
]);
$putResponse = curl_exec($ch);
curl_close($ch);
echo "Emojis fixed via PHP correctly!\n";
