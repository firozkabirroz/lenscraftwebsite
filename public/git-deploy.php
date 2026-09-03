<?php
/**
 * GitHub → cPanel auto-deploy webhook.
 *
 * One-time setup:
 * 1. Copy app/config.local.php.example → app/config.local.php on the server
 *    and set a long random deploy_secret (or set DEPLOY_SECRET env var).
 * 2. In GitHub → Settings → Webhooks → Add webhook:
 *      Payload URL:  https://YOUR-DOMAIN.com/git-deploy.php
 *      Content type: application/json
 *      Secret:       same value as deploy_secret
 *      Events:       Just the push event
 * 3. Ensure the live site is a git clone of this repo (cPanel Git or SSH),
 *    and the PHP user can run `git pull`.
 */

declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');
header('X-Robots-Tag: noindex');

$root = dirname(__DIR__);
$configFile = $root . '/app/config.local.php';
$local = is_file($configFile) ? (require $configFile) : [];
$secret = (string) (getenv('DEPLOY_SECRET') ?: ($local['deploy_secret'] ?? ''));

if ($secret === '' || strlen($secret) < 16) {
    http_response_code(503);
    echo "Deploy webhook is not configured (set deploy_secret in config.local.php).\n";
    exit;
}

$payload = file_get_contents('php://input') ?: '';
$sigHeader = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$token = (string) ($_GET['token'] ?? $_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? '');

$authorized = false;

if ($sigHeader !== '' && str_starts_with($sigHeader, 'sha256=')) {
    $expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);
    $authorized = hash_equals($expected, $sigHeader);
} elseif ($token !== '') {
    $authorized = hash_equals($secret, $token);
}

if (!$authorized) {
    http_response_code(403);
    echo "Forbidden\n";
    exit;
}

$event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? 'push';
if ($event !== 'push' && $event !== 'ping') {
    http_response_code(200);
    echo "Ignored event: {$event}\n";
    exit;
}

if ($event === 'ping') {
    echo "pong\n";
    exit;
}

$branch = 'main';
if ($payload !== '') {
    $json = json_decode($payload, true);
    if (is_array($json) && !empty($json['ref'])) {
        $branch = basename((string) $json['ref']);
    }
}

if (!preg_match('~^(main|master)$~', $branch)) {
    http_response_code(200);
    echo "Ignored branch: {$branch}\n";
    exit;
}

if (!is_dir($root . '/.git')) {
    http_response_code(500);
    echo "Not a git repository at {$root}\n";
    exit;
}

$ref = escapeshellarg($branch);
$originRef = escapeshellarg('origin/' . $branch);
$commands = [
    "git fetch origin {$ref}",
    "git reset --hard {$originRef}",
    'git clean -fd --exclude=app/config.local.php --exclude=public/uploads --exclude=storage/temp',
];

$lines = [];
$code = 0;
foreach ($commands as $command) {
    $output = [];
    exec('cd ' . escapeshellarg($root) . ' && ' . $command . ' 2>&1', $output, $status);
    $lines[] = '$ ' . $command;
    $lines = array_merge($lines, $output);
    if ($status !== 0) {
        $code = $status;
        break;
    }
}

$logDir = $root . '/storage/temp';
if (is_dir($logDir) && is_writable($logDir)) {
    file_put_contents(
        $logDir . '/deploy.log',
        '[' . date('c') . "]\n" . implode("\n", $lines) . "\n\n",
        FILE_APPEND
    );
}

http_response_code($code === 0 ? 200 : 500);
echo implode("\n", $lines) . "\n";
echo $code === 0 ? "OK\n" : "FAILED ({$code})\n";
