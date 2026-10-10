import json
import urllib.request
import urllib.error

url = 'https://n8n-95zwwqkbwlat.jkt6.sumopod.my.id/api/v1/workflows/dTA9FUgcnKQFRajZ'
headers = {
    'X-N8n-Api-Key': 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI4NzczMjI2MS1kNjA0LTRkN2UtYjk5Ni0xOTkxMGFkZTNkNzYiLCJpc3MiOiJuOG4iLCJhdWQiOiJwdWJsaWMtYXBpIiwianRpIjoiYmI4Njg1YjItNDAxOC00NDRhLWFlYWEtNzkwMThiMjNiOWUxIiwiaWF0IjoxNzkxNjIzNDE1LCJleHAiOjE3OTE2NTE2MDB9.hMF_T_07tCyIWAiHhjkdpDOMI2f5_FhGmVqbjrb2_3U',
    'Content-Type': 'application/json'
}

req = urllib.request.Request(url, headers=headers)
with urllib.request.urlopen(req) as res:
    wf = json.loads(res.read().decode('utf-8'))

for node in wf['nodes']:
    if node['name'] == 'Send a text message':
        if 'inlineKeyboard' in node['parameters']:
            for row in node['parameters']['inlineKeyboard']['rows']:
                for btn in row['row']['buttons']:
                    if '?? Membantu' in btn['text']:
                        btn['text'] = btn['text'].replace('?? Membantu', '?? Membantu')
                    if '?? Kurang' in btn['text']:
                        btn['text'] = btn['text'].replace('?? Kurang', '?? Kurang')
                        
payload = {
    'name': wf['name'],
    'nodes': wf['nodes'],
    'connections': wf.get('connections', {}),
    'settings': wf.get('settings', {})
}

req_put = urllib.request.Request(url, data=json.dumps(payload).encode('utf-8'), headers=headers, method='PUT')
with urllib.request.urlopen(req_put) as res:
    print("Emojis fixed properly via python!")
