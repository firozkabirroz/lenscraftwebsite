<?php

require __DIR__ . '/helpers.php';

spl_autoload_register(static function (string $class): void {
    if (!str_starts_with($class, 'App\\')) {
        return;
    }

    $relative = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, 4));
    $file = __DIR__ . DIRECTORY_SEPARATOR . $relative . '.php';

    if (is_file($file)) {
        require $file;
    }
});

date_default_timezone_set((string) config('timezone', 'UTC'));

if (config('app_debug')) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    ini_set('display_errors', '0');
}

if (session_status() === PHP_SESSION_NONE) {
    session_name((string) config('session_name', 'lenscraft_admin'));
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

foreach ([config('uploads')['video_dir'], config('uploads')['image_dir'], config('uploads')['thumb_dir'], config('uploads')['temp_dir']] as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
}
