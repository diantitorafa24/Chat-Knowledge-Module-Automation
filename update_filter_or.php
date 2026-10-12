<?php
$url = 'https://n8n-95zwwqkbwlat.jkt6.sumopod.my.id/api/v1/workflows/dTA9FUgcnKQFRajZ';
$apiKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI4NzczMjI2MS1kNjA0LTRkN2UtYjk5Ni0xOTkxMGFkZTNkNzYiLCJpc3MiOiJuOG4iLCJhdWQiOiJwdWJsaWMtYXBpIiwianRpIjoiYWU3ZDFhZjctMzViZS00ODgwLWEwYjYtODY2ZWRiYTk2YzBiIiwiaWF0IjoxNzkxODAxNDU2LCJleHAiOjE3OTE4MjQ0MDB9.4OLMZDuISqHqxJR7RkTSJQ_-UTEU3T1fagpm_EHR9rQ';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-N8n-Api-Key: ' . $apiKey]);
$wf = json_decode(curl_exec($ch), true);
curl_close($ch);

foreach ($wf['nodes'] as &$node) {
    if ($node['name'] === 'Filter Grup') {
        $node['parameters']['jsCode'] = <<<'JS'
const item = $input.first().json;

// Kalau bukan message (misalnya callback_query), loloskan langsung
if (!item.message) {
  return [$input.first()];
}

const chatType = item.message.chat?.type;
const text = item.message.text || item.message.caption || '';
const entities = item.message.entities || item.message.caption_entities || [];

// Faktor 1: ada @mention bot
const hasMention = entities.some(e => e.type === 'mention');

// Faktor 2: ada kata kunci "cika" (huruf besar/kecil/kombinasi)
const hasCikaKeyword = /\bcika\b/i.test(text);

// Private chat: cukup salah satu faktor terpenuhi
if (chatType === 'private') {
  if (!hasMention && !hasCikaKeyword) return [];
  return [$input.first()];
}

// Group chat: cukup salah satu faktor terpenuhi
if (hasMention || hasCikaKeyword) {
  // Bersihkan @botname dari teks sebelum diteruskan ke API
  const cleanText = text.replace(/@\w+\s*/g, '').trim();
  const newItem = JSON.parse(JSON.stringify(item));
  if (newItem.message.text) newItem.message.text = cleanText;
  if (newItem.message.caption) newItem.message.caption = cleanText;
  return [{ json: newItem }];
}

// Tidak memenuhi syarat - pesan diabaikan
return [];
JS;
        echo "Filter Grup node updated (logika OR).\n";
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
echo ($code === 200) ? "SUKSES! Filter OR diterapkan.\n" : "Error: " . substr($res, 0, 300) . "\n";
