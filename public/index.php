<?php

use App\Support\Router;

require __DIR__ . '/../app/bootstrap.php';

$router = new Router();
require __DIR__ . '/../routes.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$base = rtrim((string) config('base_url'), '/');

if ($base !== '' && str_starts_with($path, $base)) {
    $path = substr($path, strlen($base));
}

try {
    $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $path);
} catch (Throwable $e) {
    if (config('app_debug')) {
        http_response_code(500);
        echo '<pre style="background:#0B0B0B;color:#F5F5F5;padding:24px;font:13px/1.6 monospace">';
        echo e($e->getMessage()) . "\n\n" . e($e->getTraceAsString());
        echo '</pre>';
        exit;
    }

    error_log($e->getMessage());
    http_response_code(500);
    echo view('site.500', [], 'site');
}
