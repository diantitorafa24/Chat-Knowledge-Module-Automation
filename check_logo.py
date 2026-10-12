from PIL import Image
import os

path = r"C:\Users\ASUS\.gemini\antigravity\brain\2e4a1ba8-614d-4f40-ae35-70cc26814762\.user_uploaded\media_1791816091405_b700365a.png"
img = Image.open(path)
print(f"Format: {img.format}, Size: {img.size}, Mode: {img.mode}")
