import os

path = r"C:\xampp\htdocs\cika\web-admin\app\Http\Controllers\Api\AskController.php"
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Add image_urls to found=false
content = content.replace("""                'found' => false,
                'answer' => $answer,
                'sources' => [],
                'chat_log_id' => $log?->id,
            ]);""", """                'found' => false,
                'answer' => $answer,
                'sources' => [],
                'image_urls' => [],
                'chat_log_id' => $log?->id,
            ]);""")

# Add image_urls to found=true
content = content.replace("""            ], $search['results']),
            'chat_log_id' => $log?->id,
        ]);""", """            ], $search['results']),
            'image_urls' => $search['image_urls'] ?? [],
            'chat_log_id' => $log?->id,
        ]);""")

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("AskController updated.")
