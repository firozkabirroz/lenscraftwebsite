<?php
/**
 * Router for PHP's built-in server: php -S localhost:8000 -t public public/router.php
 * Existing files (CSS, JS, uploads) are served as-is; everything else goes to index.php.
 */

$uri  = urldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
$file = __DIR__ . $uri;

if ($uri !== '/' && is_file($file)) {
    return false;
}

require __DIR__ . '/index.php';
