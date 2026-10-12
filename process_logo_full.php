<?php
$src = 'C:\Users\ASUS\.gemini\antigravity\brain\2e4a1ba8-614d-4f40-ae35-70cc26814762\.user_uploaded\media_1791816091405_b700365a.png';
$im = imagecreatefrompng($src);
$w = imagesx($im);
$h = imagesy($im);

// Create output with transparent background
$out = imagecreatetruecolor($w, $h);
imagealphablending($out, false);
imagesavealpha($out, true);
$transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
imagefilledrectangle($out, 0, 0, $w, $h, $transparent);

for ($x = 0; $x < $w; $x++) {
    for ($y = 0; $y < $h; $y++) {
        $rgb = imagecolorat($im, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;

        // Kill near-white background (the light gray/white box background)
        $brightness = ($r + $g + $b) / 3;
        if ($r > 230 && $g > 230 && $b > 230) {
            // Fully transparent for white/light-gray background
            imagesetpixel($out, $x, $y, $transparent);
            continue;
        }

        // Black pixels = the shadow/fill of CIKA letters → make semi-transparent blue glow
        if ($r < 40 && $g < 40 && $b < 40) {
            // Very subtle translucent glow (mostly transparent)
            $alpha = 100; // 100 out of 127 = very transparent
            $c = imagecolorallocatealpha($out, 100, 160, 255, $alpha);
            imagesetpixel($out, $x, $y, $c);
            continue;
        }

        // Dark gray pixels (near black, for outlines and subtitle text)
        if ($r < 80 && $g < 80 && $b < 80) {
            // Make slightly transparent white-blue so it shows on dark bg
            $alpha = 30; // somewhat visible
            $c = imagecolorallocatealpha($out, 200, 220, 255, $alpha);
            imagesetpixel($out, $x, $y, $c);
            continue;
        }

        // Medium dark (dark grays for subtitle text area)
        if ($r < 130 && $g < 130 && $b < 130 && abs($r - $g) < 20 && abs($g - $b) < 20) {
            // Neutral gray → convert to light gray/white for dark bg readability
            $alpha = 20;
            $c = imagecolorallocatealpha($out, 190, 200, 220, $alpha);
            imagesetpixel($out, $x, $y, $c);
            continue;
        }

        // Keep everything else as-is (blues, the speech bubble colors)
        $c = imagecolorallocatealpha($out, $r, $g, $b, 0);
        imagesetpixel($out, $x, $y, $c);
    }
}

imagepng($out, 'C:\xampp\htdocs\cika\web-admin\public\images\cika-logo-full.png');
imagedestroy($im);
imagedestroy($out);
echo "Logo penuh berhasil diproses: cika-logo-full.png ({$w}x{$h})\n";
