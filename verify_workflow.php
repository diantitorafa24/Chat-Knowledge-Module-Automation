<?php
// Verify - check the actual emoji value stored in n8n now
$url = 'https://n8n-95zwwqkbwlat.jkt6.sumopod.my.id/api/v1/workflows/dTA9FUgcnKQFRajZ';
$apiKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI4NzczMjI2MS1kNjA0LTRkN2UtYjk5Ni0xOTkxMGFkZTNkNzYiLCJpc3MiOiJuOG4iLCJhdWQiOiJwdWJsaWMtYXBpIiwianRpIjoiYmI4Njg1YjItNDAxOC00NDRhLWFlYWEtNzkwMThiMjNiOWUxIiwiaWF0IjoxNzkxNjIzNDE1LCJleHAiOjE3OTE2NTE2MDB9.hMF_T_07tCyIWAiHhjkdpDOMI2f5_FhGmVqbjrb2_3U';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-N8n-Api-Key: ' . $apiKey]);
$res = curl_exec($ch);
curl_close($ch);
$wf = json_decode($res, true);
foreach ($wf['nodes'] as $node) {
    if ($node['name'] === 'Send a text message') {
        $buttons = $node['parameters']['inlineKeyboard']['rows'][0]['row']['buttons'];
        foreach ($buttons as $btn) {
            echo "Button: " . $btn['text'] . "\n";
        }
    }
}
echo "\nNode list:\n";
foreach ($wf['nodes'] as $n) { echo "- " . $n['name'] . "\n"; }
