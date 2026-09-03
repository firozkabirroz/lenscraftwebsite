<?php
/**
 * Base configuration. Copy config.local.php.example to config.local.php
 * to override any of these values without touching this file.
 */

return array_merge([
    'app_name'    => 'LensCraft Production',
    'app_env'     => 'local',          // local | production
    'app_debug'   => true,
    'base_url'    => '',               // e.g. /lenscraft/public when served from a sub folder

    'db' => [
        'host'    => '127.0.0.1',
        'port'    => 3306,
        'name'    => 'lenscraft',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],

    'uploads' => [
        'video_dir'   => __DIR__ . '/../public/uploads/videos',
        'image_dir'   => __DIR__ . '/../public/uploads/images',
        'thumb_dir'   => __DIR__ . '/../public/uploads/thumbs',
        'temp_dir'    => __DIR__ . '/../storage/temp',
        'max_video'   => 2 * 1024 * 1024 * 1024,   // 2 GB
        'max_image'   => 12 * 1024 * 1024,         // 12 MB
        'chunk_size'  => 4 * 1024 * 1024,          // must match assets/js/upload.js
        'video_types' => ['video/mp4', 'video/quicktime', 'video/x-matroska', 'video/webm'],
        'image_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml'],
        'doc_types'   => ['application/pdf', 'text/plain'],
    ],

    'session_name' => 'lenscraft_admin',
    'timezone'     => 'Asia/Dhaka',
], file_exists(__DIR__ . '/config.local.php') ? require __DIR__ . '/config.local.php' : []);
