<?php
$path = 'C:\Users\ASUS\.gemini\antigravity\brain\2e4a1ba8-614d-4f40-ae35-70cc26814762\.user_uploaded\media_1791816091405_b700365a.png';
$im = imagecreatefrompng($path);
$w = imagesx($im);
$h = imagesy($im);

// Let's create an icon crop for the speech bubble (roughly left side from x=40 to x=320, y=30 to y=320)
// Let's find bounding box of non-white pixels on the left side
$minX = $w; $maxX = 0; $minY = $h; $maxY = 0;
for ($x = 20; $x < 360; $x++) {
    for ($y = 20; $y < $h - 20; $y++) {
        $rgb = imagecolorat($im, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        if ($r < 240 || $g < 240 || $b < 240) {
            if ($x < $minX) $minX = $x;
            if ($x > $maxX) $maxX = $x;
            if ($y < $minY) $minY = $y;
            if ($y > $maxY) $maxY = $y;
        }
    }
}

echo "Icon BBox: X ($minX -> $maxX), Y ($minY -> $maxY)\n";

// Crop icon with transparent background
$iconW = ($maxX - $minX) + 20;
$iconH = ($maxY - $minY) + 20;
$icon = imagecreatetruecolor($iconW, $iconH);
imagealphablending($icon, false);
imagesavealpha($icon, true);
$transparent = imagecolorallocatealpha($icon, 0, 0, 0, 127);
imagefilledrectangle($icon, 0, 0, $iconW, $iconH, $transparent);

for ($x = 0; $x < $iconW; $x++) {
    for ($y = 0; $y < $iconH; $y++) {
        $srcX = $minX - 10 + $x;
        $srcY = $minY - 10 + $y;
        if ($srcX >= 0 && $srcX < $w && $srcY >= 0 && $srcY < $h) {
            $rgb = imagecolorat($im, $srcX, $srcY);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            if ($r > 248 && $g > 248 && $b > 248) {
                imagesetpixel($icon, $x, $y, $transparent);
            } else {
                $c = imagecolorallocatealpha($icon, $r, $g, $b, 0);
                imagesetpixel($icon, $x, $y, $c);
            }
        }
    }
}
imagepng($icon, 'C:\xampp\htdocs\cika\web-admin\public\images\cika-icon.png');
echo "Created cika-icon.png successfully!\n";
