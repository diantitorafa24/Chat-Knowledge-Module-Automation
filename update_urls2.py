import json

with open("C:/xampp/htdocs/cika/workflow_fetch.json", "r", encoding="utf-16") as f:
    wf = json.load(f)

new_url_base = "https://accessing-raleigh-suite-cst.trycloudflare.com"

nodes = wf.get("nodes", [])
for n in nodes:
    if n['name'] == 'Tanya CIKA':
        n['parameters']['url'] = new_url_base + "/api/ask"
    elif n['name'] == 'HTTP Request' and n['type'] == 'n8n-nodes-base.httpRequest':
        n['parameters']['url'] = new_url_base + "/api/feedback"
    elif n['name'] == 'Answer Query a callback':
        # Don't need to change callback, it just replies to TG
        pass

payload = {
    "name": wf.get("name"),
    "nodes": nodes,
    "connections": wf.get("connections", {}),
    "settings": wf.get("settings", {})
}

with open("C:/xampp/htdocs/cika/workflow_payload.json", "w", encoding="utf-8") as f:
    json.dump(payload, f, indent=2)

print("Nodes updated.")
