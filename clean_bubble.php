<?php
$src = 'C:\Users\ASUS\.gemini\antigravity\brain\2e4a1ba8-614d-4f40-ae35-70cc26814762\.user_uploaded\media_1791816091405_b700365a.png';
$im = imagecreatefrompng($src);
$w = imagesx($im);
$h = imagesy($im);

// Speech bubble coordinates:
// Starts around x=140, ends around x=350, y=70 to y=280
// Let's create an exact, clean crop of just the bubble!
// The bubble outline color is approximately RGB(80..120, 90..130, 100..140).
// The question mark is RGB(0..40, 60..120, 200..255).
$minX = $w; $maxX = 0; $minY = $h; $maxY = 0;

for ($x = 100; $x < 360; $x++) {
    for ($y = 50; $y < 300; $y++) {
        $rgb = imagecolorat($im, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        // Non-white pixel belonging to the bubble or question mark
        if (($r < 200 || $g < 200 || $b < 200)) {
            if ($x < $minX) $minX = $x;
            if ($x > $maxX) $maxX = $x;
            if ($y < $minY) $minY = $y;
            if ($y > $maxY) $maxY = $y;
        }
    }
}

echo "Accurate Bubble BBox: X ($minX -> $maxX), Y ($minY -> $maxY)\n";

$cropW = ($maxX - $minX) + 16;
$cropH = ($maxY - $minY) + 16;
$out = imagecreatetruecolor($cropW, $cropH);
imagealphablending($out, false);
imagesavealpha($out, true);
$transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
imagefilledrectangle($out, 0, 0, $cropW, $cropH, $transparent);

for ($x = 0; $x < $cropW; $x++) {
    for ($y = 0; $y < $cropH; $y++) {
        $sx = $minX - 8 + $x;
        $sy = $minY - 8 + $y;
        if ($sx >= 0 && $sx < $w && $sy >= 0 && $sy < $h) {
            $rgb = imagecolorat($im, $sx, $sy);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            
            // Background is white/light gray
            if ($r > 235 && $g > 235 && $b > 235) {
                imagesetpixel($out, $x, $y, $transparent);
            } else {
                $c = imagecolorallocatealpha($out, $r, $g, $b, 0);
                imagesetpixel($out, $x, $y, $c);
            }
        }
    }
}

imagepng($out, 'C:\xampp\htdocs\cika\web-admin\public\images\cika-bubble.png');
echo "Saved clean cika-bubble.png\n";
