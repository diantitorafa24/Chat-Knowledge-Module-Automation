import json

with open("C:/xampp/htdocs/cika/workflow_payload.json", "r", encoding="utf-8") as f:
    wf = json.load(f)

new_url_base = "https://accessing-raleigh-suite-cst.trycloudflare.com"

nodes = wf.get("nodes", [])
for n in nodes:
    if n['name'] == 'Tanya CIKA':
        n['parameters']['url'] = new_url_base + "/api/ask"
    elif n['name'] == 'HTTP Request' and n['type'] == 'n8n-nodes-base.httpRequest':
        n['parameters']['url'] = new_url_base + "/api/feedback"

with open("C:/xampp/htdocs/cika/workflow_payload.json", "w", encoding="utf-8") as f:
    json.dump(wf, f, indent=2)

print("Nodes updated.")
