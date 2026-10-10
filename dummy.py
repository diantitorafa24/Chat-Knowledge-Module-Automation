import os

path = r"C:\xampp\htdocs\cika\web-admin\routes\web.php"
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# I will remove the route later, but for now I'll create an artisan command.
