<?php
$path = 'C:\Users\ASUS\.gemini\antigravity\brain\2e4a1ba8-614d-4f40-ae35-70cc26814762\.user_uploaded\media_1791816091405_b700365a.png';
$im = imagecreatefrompng($path);
$w = imagesx($im);
$h = imagesy($im);

// Let's create an image directory in public
if (!is_dir('C:\xampp\htdocs\cika\web-admin\public\images')) {
    mkdir('C:\xampp\htdocs\cika\web-admin\public\images', 0777, true);
}

// Copy original to public
copy($path, 'C:\xampp\htdocs\cika\web-admin\public\images\cika-logo-original.png');

// Process image: make black parts transparent with smooth alpha
$out = imagecreatetruecolor($w, $h);
imagealphablending($out, false);
imagesavealpha($out, true);
$transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
imagefilledrectangle($out, 0, 0, $w, $h, $transparent);

for ($x = 0; $x < $w; $x++) {
    for ($y = 0; $y < $h; $y++) {
        $rgba = imagecolorat($im, $x, $y);
        $r = ($rgba >> 16) & 0xFF;
        $g = ($rgba >> 8) & 0xFF;
        $b = $rgba & 0xFF;
        $a = ($rgba >> 24) & 0x7F;

        // Check if pixel is white background (around the logo box)
        // If it's near white background:
        if ($r > 245 && $g > 245 && $b > 245) {
            // Keep transparent or soft
            imagesetpixel($out, $x, $y, $transparent);
        } elseif ($r < 35 && $g < 35 && $b < 35) {
            // This is the black shadow/fill of CIKA letters!
            // Make it semi-transparent blur / subtle glass shadow (e.g. 80-90% transparent)
            $newColor = imagecolorallocatealpha($out, 59, 130, 246, 110); // subtle blue glass tint
            imagesetpixel($out, $x, $y, $newColor);
        } else {
            // Normal color
            $c = imagecolorallocatealpha($out, $r, $g, $b, $a);
            imagesetpixel($out, $x, $y, $c);
        }
    }
}

imagepng($out, 'C:\xampp\htdocs\cika\web-admin\public\images\cika-logo-transparent.png');
echo "Saved cika-logo-transparent.png\n";
