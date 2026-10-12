<?php
$path = 'C:\Users\ASUS\.gemini\antigravity\brain\2e4a1ba8-614d-4f40-ae35-70cc26814762\.user_uploaded\media_1791816091405_b700365a.png';
$size = getimagesize($path);
echo "Width: {$size[0]}, Height: {$size[1]}, Mime: {$size['mime']}\n";
