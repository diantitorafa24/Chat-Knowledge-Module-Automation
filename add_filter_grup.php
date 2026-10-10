<?php
$url = 'https://n8n-95zwwqkbwlat.jkt6.sumopod.my.id/api/v1/workflows/dTA9FUgcnKQFRajZ';
$apiKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI4NzczMjI2MS1kNjA0LTRkN2UtYjk5Ni0xOTkxMGFkZTNkNzYiLCJpc3MiOiJuOG4iLCJhdWQiOiJwdWJsaWMtYXBpIiwianRpIjoiYmI4Njg1YjItNDAxOC00NDRhLWFlYWEtNzkwMThiMjNiOWUxIiwiaWF0IjoxNzkxNjIzNDE1LCJleHAiOjE3OTE2NTE2MDB9.hMF_T_07tCyIWAiHhjkdpDOMI2f5_FhGmVqbjrb2_3U';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-N8n-Api-Key: ' . $apiKey]);
$wf = json_decode(curl_exec($ch), true);
curl_close($ch);

// Tambah node Code baru: Filter Grup
$filterNode = [
    'id' => 'cika_filter_grup',
    'name' => 'Filter Grup',
    'type' => 'n8n-nodes-base.code',
    'typeVersion' => 2,
    'position' => [-1200, -16], // posisi sebelum If node (-992)
    'parameters' => [
        'jsCode' => <<<'JS'
const item = $input.first().json;

// Kalau bukan message (misalnya callback_query), loloskan langsung
if (!item.message) {
  return [$input.first()];
}

const chatType = item.message.chat?.type;
const text = item.message.text || item.message.caption || '';
const entities = item.message.entities || item.message.caption_entities || [];

// Private chat: selalu loloskan
if (chatType === 'private') {
  return [$input.first()];
}

// Group/supergroup: hanya loloskan kalau ada mention (@username)
const hasMention = entities.some(e => e.type === 'mention');
if (hasMention) {
  // Bersihkan teks dari @botname sebelum diteruskan
  const cleanText = text.replace(/@\w+\s*/g, '').trim();
  const newItem = JSON.parse(JSON.stringify(item));
  if (newItem.message.text) newItem.message.text = cleanText;
  if (newItem.message.caption) newItem.message.caption = cleanText;
  return [{ json: newItem }];
}

// Tidak loloskan (return kosong = hentikan alur)
return [];
JS
    ]
];

// Pindahkan node Telegram Trigger ke posisi lebih kiri
foreach ($wf['nodes'] as &$node) {
    if ($node['name'] === 'Telegram Trigger') {
        $node['position'] = [-1440, -16];
    }
}
unset($node);

// Tambah node Filter Grup ke list
$wf['nodes'][] = $filterNode;

// Update connections:
// Telegram Trigger ? Filter Grup (bukan langsung ke If)
// Filter Grup ? If
$wf['connections']['Telegram Trigger']['main'][0] = [
    ['node' => 'Filter Grup', 'type' => 'main', 'index' => 0]
];
$wf['connections']['Filter Grup']['main'][0] = [
    ['node' => 'If', 'type' => 'main', 'index' => 0]
];

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
echo ($code === 200) ? "Berhasil! Node Filter Grup ditambahkan.\n" : $res . "\n";
