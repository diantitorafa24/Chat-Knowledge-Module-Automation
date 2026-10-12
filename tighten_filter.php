<?php
$url = 'https://n8n-95zwwqkbwlat.jkt6.sumopod.my.id/api/v1/workflows/dTA9FUgcnKQFRajZ';
$apiKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI4NzczMjI2MS1kNjA0LTRkN2UtYjk5Ni0xOTkxMGFkZTNkNzYiLCJpc3MiOiJuOG4iLCJhdWQiOiJwdWJsaWMtYXBpIiwianRpIjoiYWU3ZDFhZjctMzViZS00ODgwLWEwYjYtODY2ZWRiYTk2YzBiIiwiaWF0IjoxNzkxODAxNDU2LCJleHAiOjE3OTE4MjQ0MDB9.4OLMZDuISqHqxJR7RkTSJQ_-UTEU3T1fagpm_EHR9rQ';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-N8n-Api-Key: ' . $apiKey]);
$res = curl_exec($ch);
curl_close($ch);

$wf = json_decode($res, true);

foreach ($wf['nodes'] as &$node) {
    if ($node['name'] === 'Filter Grup') {
        $node['parameters']['jsCode'] = <<<'JS'
const item = $input.first().json;

// Kalau bukan message (misalnya callback_query dari tombol feedback), loloskan
if (!item.message) {
  return [$input.first()];
}

const chatType = item.message.chat?.type;
const text = (item.message.text || item.message.caption || '').trim();

// 1. Cek apakah bot di-tag secara SPESIFIK (@cika_module_bot)
const isTagged = /@cika_module_bot\b/i.test(text);

// 2. Cek apakah ada kata kunci "cika" (mandiri, case-insensitive)
const hasCikaKeyword = /\bcika\b/i.test(text);

// Di PRIVATE chat: selalu layani (chat 1-on-1 langsung dengan bot)
if (chatType === 'private') {
  return [$input.first()];
}

// Di GRUP / SUPERGROUP:
// WAJIB di-tag SPESIFIK (@cika_module_bot) ATAU mengandung kata "cika"
if (isTagged || hasCikaKeyword) {
  // Bersihkan HANYA tag @cika_module_bot dari pertanyaan
  const cleanText = text.replace(/@cika_module_bot\b/gi, '').trim();
  const newItem = JSON.parse(JSON.stringify(item));
  if (newItem.message.text) newItem.message.text = cleanText;
  if (newItem.message.caption) newItem.message.caption = cleanText;
  return [{ json: newItem }];
}

// Jika di grup dan tidak ada tag @cika_module_bot atau kata "cika", ABAIKAN TOTAL
return [];
JS;
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
echo ($code === 200) ? "SUKSES! Filter diperketat: hanya tag @cika_module_bot atau kata 'cika' yang lolos.\n" : substr($res, 0, 300) . "\n";
