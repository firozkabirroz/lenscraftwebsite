<?php
/**
 * Creates the placeholder stills referenced by database/seed.sql so the demo
 * site renders with images before real photography is uploaded.
 *
 *   php tools/generate-placeholders.php
 */

$targets = [
    'river-still-01.jpg'  => ['River of Voices', 'Documentary · 2025'],
    'aarong-frame.jpg'    => ['Aarong Winter', 'Commercial · 2025'],
    'shomoy-poster.jpg'   => ['Shomoy', 'Film & Natok · 2024'],
    'studio-bts-04.jpg'   => ['Behind the scenes', 'LensCraft crew'],
    'night-market-02.jpg' => ['Night Market', 'Documentary · 2024'],
    'boardroom-still.jpg' => ['Boardroom Trust', 'Corporate AV · 2025'],
];

$dir = __DIR__ . '/../public/uploads/images';
if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
    exit("Could not create {$dir}\n");
}

foreach ($targets as $filename => [$title, $subtitle]) {
    $width = 1600;
    $height = 1000;
    $image = imagecreatetruecolor($width, $height);

    // Vertical charcoal gradient with a warm gold wash in one corner.
    for ($y = 0; $y < $height; $y++) {
        $shade = (int) (26 - 14 * ($y / $height));
        imagefilledrectangle($image, 0, $y, $width, $y, imagecolorallocate($image, $shade, $shade, $shade));
    }

    for ($i = 0; $i < 260; $i++) {
        $alpha = (int) (110 - $i / 3);
        $gold = imagecolorallocatealpha($image, 196, 163, 90, max(60, $alpha));
        imagefilledellipse($image, 260, 180, 900 - $i * 2, 620 - $i * 2, $gold);
    }

    $line = imagecolorallocate($image, 60, 60, 60);
    imagerectangle($image, 40, 40, $width - 41, $height - 41, $line);

    $white = imagecolorallocate($image, 245, 245, 245);
    $muted = imagecolorallocate($image, 154, 154, 154);
    imagestring($image, 5, 80, $height - 150, strtoupper($title), $white);
    imagestring($image, 3, 80, $height - 120, strtoupper($subtitle), $muted);
    imagestring($image, 2, 80, 80, 'LENSCRAFT PRODUCTION — PLACEHOLDER STILL', $muted);

    imagejpeg($image, $dir . '/' . $filename, 82);
    imagedestroy($image);

    echo 'created ' . $filename . PHP_EOL;
}
