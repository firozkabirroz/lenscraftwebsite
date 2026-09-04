<?php

/**
 * Global helper functions used by controllers and views.
 */

function config(?string $key = null, mixed $default = null): mixed
{
    static $config = null;

    if ($config === null) {
        $config = require __DIR__ . '/config.php';
    }

    if ($key === null) {
        return $config;
    }

    return $config[$key] ?? $default;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = '/'): string
{
    $base = rtrim((string) config('base_url'), '/');
    $path = '/' . ltrim($path, '/');

    return $base . ($path === '/' ? '/' : rtrim($path, '/'));
}

function asset(string $path): string
{
    $base = rtrim((string) config('base_url'), '/');

    return $base . '/assets/' . ltrim($path, '/');
}

function uploaded(?string $path): string
{
    if ($path === null || $path === '') {
        return '';
    }
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }

    return rtrim((string) config('base_url'), '/') . '/uploads/' . ltrim($path, '/');
}

/** Brand-strip logos: prefer auto-fitted PNG when available. */
function brand_logo(?string $path): string
{
    $fitted = \App\Support\Uploader::logoPath($path);

    return uploaded($fitted ?: $path);
}

function redirect(string $path): never
{
    header('Location: ' . (str_starts_with($path, 'http') ? $path : url($path)));
    exit;
}

function back(string $fallback = '/'): never
{
    redirect($_SERVER['HTTP_REFERER'] ?? url($fallback));
}

function request(string $key, mixed $default = null): mixed
{
    return $_REQUEST[$key] ?? $default;
}

function input(string $key, mixed $default = null): mixed
{
    $value = $_POST[$key] ?? $default;

    return is_string($value) ? trim($value) : $value;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function current_path(): string
{
    $base = rtrim((string) config('base_url'), '/');
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

    if ($base !== '' && str_starts_with($path, $base)) {
        $path = substr($path, strlen($base));
    }

    return '/' . trim($path, '/');
}

function is_active(string $prefix): bool
{
    $path = current_path();

    return $path === $prefix || str_starts_with($path, rtrim($prefix, '/') . '/');
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

    if (!is_string($token) || $token === '' || !hash_equals(csrf_token(), $token)) {
        http_response_code(419);
        exit('Session expired. Please refresh the page and try again.');
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

function flash_all(): array
{
    $messages = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);

    return $messages;
}

function old(string $key, mixed $default = ''): mixed
{
    return $_SESSION['_old'][$key] ?? $default;
}

function remember_old(array $data): void
{
    $_SESSION['_old'] = $data;
}

function clear_old(): void
{
    unset($_SESSION['_old']);
}

function view(string $template, array $data = [], ?string $layout = null): string
{
    $file = __DIR__ . '/Views/' . str_replace('.', '/', $template) . '.php';

    if (!is_file($file)) {
        throw new RuntimeException('View not found: ' . $template);
    }

    extract($data, EXTR_SKIP);
    ob_start();
    require $file;
    $content = (string) ob_get_clean();

    if ($layout !== null) {
        $layoutFile = __DIR__ . '/Views/layouts/' . $layout . '.php';
        if (!is_file($layoutFile)) {
            throw new RuntimeException('Layout not found: ' . $layout);
        }
        ob_start();
        require $layoutFile;
        $content = (string) ob_get_clean();
    }

    return $content;
}

function render(string $template, array $data = [], ?string $layout = null): void
{
    echo view($template, $data, $layout);
}

function partial(string $template, array $data = []): void
{
    echo view('partials.' . $template, $data);
}

function json_out(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function abort(int $status, string $message = ''): never
{
    http_response_code($status);

    if ($status === 404) {
        echo view('site.404', ['message' => $message], 'site');
        exit;
    }

    exit($message !== '' ? $message : 'Error ' . $status);
}

function slugify(string $value): string
{
    $value = preg_replace('~[^\pL\d]+~u', '-', $value) ?? '';
    $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value) ?: $value;
    $value = strtolower(trim(preg_replace('~[^-\w]+~', '', $value) ?? '', '-'));

    return $value !== '' ? $value : 'item-' . substr(md5(random_bytes(8)), 0, 6);
}

function human_size(int|float|null $bytes): string
{
    $bytes = (float) $bytes;
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }

    return round($bytes, $bytes < 10 && $i > 0 ? 1 : 0) . ' ' . $units[$i];
}

function money(int|float|null $amount, string $currency = 'BDT'): string
{
    return $currency . ' ' . number_format((float) $amount);
}

function time_ago(?string $datetime): string
{
    if (!$datetime) {
        return '—';
    }

    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) {
        return 'just now';
    }
    if ($diff < 3600) {
        return floor($diff / 60) . ' min ago';
    }
    if (date('Y-m-d') === date('Y-m-d', $timestamp)) {
        return 'Today ' . date('H:i', $timestamp);
    }
    if (date('Y-m-d', strtotime('-1 day')) === date('Y-m-d', $timestamp)) {
        return 'Yesterday ' . date('H:i', $timestamp);
    }

    return date('M d · H:i', $timestamp);
}

function date_pretty(?string $date, string $format = 'M d, Y'): string
{
    if (!$date || $date === '0000-00-00') {
        return '—';
    }

    return date($format, strtotime($date));
}

function excerpt(?string $text, int $limit = 140): string
{
    $text = trim(preg_replace('/\s+/', ' ', (string) $text) ?? '');

    if (mb_strlen($text) <= $limit) {
        return $text;
    }

    return mb_substr($text, 0, $limit - 1) . '…';
}

function duration_format(?int $seconds): string
{
    if (!$seconds) {
        return '—';
    }

    return sprintf('%02d:%02d', intdiv($seconds, 60), $seconds % 60);
}

/**
 * Turn a YouTube / Vimeo watch URL into an embeddable player URL.
 * Unlisted Vimeo links keep their privacy hash — without it the player 404s.
 */
function embed_url(?string $url): string
{
    $url = trim((string) $url);
    if ($url === '') {
        return '';
    }

    if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([\w-]+)~', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1];
    }

    $hash = '';
    if (preg_match('~[?&]h=([a-f0-9]+)~i', $url, $hm)) {
        $hash = $hm[1];
    } elseif (preg_match('~vimeo\.com/\d+/([a-f0-9]{6,})~i', $url, $hm)) {
        $hash = $hm[1];
    }

    if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $url, $m)) {
        $embed = 'https://player.vimeo.com/video/' . $m[1];

        return $hash !== '' ? $embed . '?h=' . $hash : $embed;
    }

    return $url;
}

/**
 * Player URL with extra query params merged onto any existing ones (e.g. Vimeo h=).
 */
function embed_src(?string $url, array $params = []): string
{
    $base = embed_url($url);
    if ($base === '') {
        return '';
    }

    $parts = parse_url($base);
    $existing = [];
    if (!empty($parts['query'])) {
        parse_str($parts['query'], $existing);
    }

    $query = array_merge($existing, $params);
    $origin = ($parts['scheme'] ?? 'https') . '://' . ($parts['host'] ?? '');
    $path = $parts['path'] ?? '';

    return $origin . $path . ($query !== [] ? '?' . http_build_query($query) : '');
}

function embed_provider(?string $url): string
{
    $url = (string) $url;

    if (str_contains($url, 'youtu')) {
        return 'youtube';
    }
    if (str_contains($url, 'vimeo')) {
        return 'vimeo';
    }

    return $url === '' ? '' : 'external';
}

/**
 * Data attributes for the hover video preview on work cards.
 * Local files play in a <video>, YouTube/Vimeo in a muted background iframe.
 */
function preview_attrs(?string $url): string
{
    $url = trim((string) $url);
    if ($url === '') {
        return '';
    }

    $provider = embed_provider($url);

    if ($provider === 'youtube') {
        $id = basename((string) parse_url(embed_url($url), PHP_URL_PATH));
        $src = embed_src($url, [
            'autoplay' => 1,
            'mute' => 1,
            'controls' => 0,
            'loop' => 1,
            'playlist' => $id,
            'modestbranding' => 1,
            'playsinline' => 1,
        ]);

        return ' data-preview="' . e($src) . '" data-preview-kind="iframe"';
    }

    if ($provider === 'vimeo') {
        $src = embed_src($url, [
            'background' => 1,
            'autoplay' => 1,
            'muted' => 1,
            'loop' => 1,
            'autopause' => 0,
        ]);

        return ' data-preview="' . e($src) . '" data-preview-kind="iframe"';
    }

    return ' data-preview="' . e($url) . '" data-preview-kind="video"';
}

function status_tone(string $status): string
{
    return match (strtolower($status)) {
        'published', 'confirmed', 'ready', 'complete', 'completed' => 'success',
        'draft', 'archived', 'read' => 'muted',
        'cancelled', 'failed' => 'danger',
        default => 'accent',
    };
}
