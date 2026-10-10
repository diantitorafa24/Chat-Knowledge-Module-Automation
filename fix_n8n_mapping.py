import json
import urllib.request
import urllib.error

url = 'https://n8n-95zwwqkbwlat.jkt6.sumopod.my.id/api/v1/workflows/dTA9FUgcnKQFRajZ'
headers = {
    'X-N8n-Api-Key': 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI4NzczMjI2MS1kNjA0LTRkN2UtYjk5Ni0xOTkxMGFkZTNkNzYiLCJpc3MiOiJuOG4iLCJhdWQiOiJwdWJsaWMtYXBpIiwianRpIjoiYmI4Njg1YjItNDAxOC00NDRhLWFlYWEtNzkwMThiMjNiOWUxIiwiaWF0IjoxNzkxNjIzNDE1LCJleHAiOjE3OTE2NTE2MDB9.hMF_T_07tCyIWAiHhjkdpDOMI2f5_FhGmVqbjrb2_3U',
    'Content-Type': 'application/json'
}

with open("C:/xampp/htdocs/cika/n8n_current.json", "r", encoding="utf-8") as f:
    wf = json.load(f)

for node in wf['nodes']:
    # Fix the text mapping referencing the original Tanya CIKA node instead of previous node
    if node['name'] == 'Format Jawaban Ditemukan':
        for assignment in node['parameters']['assignments']['assignments']:
            if assignment['name'] == 'pesan_balasan':
                assignment['value'] = "={{ $('Tanya CIKA').first().json.answer }}"
                
    # Fix the Emojis properly
    if node['name'] == 'Send a text message':
        if 'inlineKeyboard' in node['parameters']:
            for row in node['parameters']['inlineKeyboard']['rows']:
                for btn in row['row']['buttons']:
                    if '?? Membantu' in btn['text'] or 'Membantu' in btn['text']:
                        btn['text'] = '?? Membantu'
                    if '?? Kurang' in btn['text'] or 'Kurang' in btn['text']:
                        btn['text'] = '?? Kurang'
                        
payload = {
    'name': wf['name'],
    'nodes': wf['nodes'],
    'connections': wf.get('connections', {}),
    'settings': wf.get('settings', {})
}

req_put = urllib.request.Request(url, data=json.dumps(payload, ensure_ascii=False).encode('utf-8'), headers=headers, method='PUT')
with urllib.request.urlopen(req_put) as res:
    print("Fixed n8n mapping and emojis!")
