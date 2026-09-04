<?php

use App\Models\Message;

/** @var string $content */
$user = $user ?? [];
$activeNav = $activeNav ?? '';
$unread = Message::unreadCount();

$navItems = [
    ['key' => 'dashboard', 'label' => 'Dashboard', 'url' => url('/admin')],
    ['key' => 'content',   'label' => 'Content',   'url' => url('/admin/content')],
    ['key' => 'projects',  'label' => 'Projects',  'url' => url('/admin/projects')],
    ['key' => 'videos',    'label' => 'Videos',    'url' => url('/admin/videos')],
    ['key' => 'media',     'label' => 'Media',     'url' => url('/admin/media')],
    ['key' => 'bookings',  'label' => 'Bookings',  'url' => url('/admin/bookings')],
    ['key' => 'messages',  'label' => 'Messages',  'url' => url('/admin/messages'), 'badge' => $unread],
    ['key' => 'clients',   'label' => 'Clients',   'url' => url('/admin/clients')],
    ['key' => 'brands',    'label' => 'Brands',    'url' => url('/admin/brands')],
    ['key' => 'packages',  'label' => 'Packages',  'url' => url('/admin/packages')],
    ['key' => 'team',      'label' => 'Team',      'url' => url('/admin/team')],
    ['key' => 'analytics', 'label' => 'Analytics', 'url' => url('/admin/analytics')],
    ['key' => 'settings',  'label' => 'Settings',  'url' => url('/admin/settings')],
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(($pageTitle ?? 'Admin') . ' — LensCraft Admin') ?></title>
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="<?= asset('css/site.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <link rel="icon" href="<?= asset('img/logo.svg') ?>" type="image/svg+xml">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
</head>
<body class="admin">
<aside class="sidebar" id="sidebar">
    <a class="sidebar__brand" href="<?= url('/admin') ?>">
        <img src="<?= asset('img/logo.svg') ?>" alt="" width="28" height="28">
        <span>LensCraft<small>ADMIN</small></span>
    </a>
    <nav class="sidebar__nav">
        <?php foreach ($navItems as $item): ?>
            <a href="<?= $item['url'] ?>" class="<?= $activeNav === $item['key'] ? 'is-active' : '' ?>">
                <?= e($item['label']) ?>
                <?php if (!empty($item['badge'])): ?><i class="dot"><?= (int) $item['badge'] ?></i><?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar__user">
        <span class="avatar"><?= e(mb_substr($user['name'] ?? 'A', 0, 1)) ?></span>
        <div>
            <strong><?= e($user['name'] ?? 'Admin') ?></strong>
            <small><?= e(ucfirst($user['role'] ?? 'editor')) ?> · <a href="<?= url('/admin/logout') ?>">Log out</a></small>
        </div>
    </div>
</aside>

<div class="main">
    <header class="topbar">
        <button class="topbar__burger" id="sidebarToggle" aria-label="Menu">☰</button>
        <div class="topbar__title">
            <h1><?= e($pageTitle ?? 'Dashboard') ?></h1>
            <p><?= e($pageSubtitle ?? '') ?></p>
        </div>
        <form class="topbar__search" method="get" action="<?= e(strtok($_SERVER['REQUEST_URI'] ?? url('/admin'), '?')) ?>">
            <input type="search" name="q" value="<?= e((string) request('q', '')) ?>" placeholder="Search…">
        </form>
        <?php if (!empty($actionLabel)): ?>
            <a class="btn btn--primary btn--sm" href="<?= e($actionUrl ?? '#') ?>"><?= e($actionLabel) ?></a>
        <?php endif; ?>
    </header>

    <div class="content">
        <?php foreach (flash_all() as $message): ?>
            <div class="flash flash--<?= e($message['type']) ?>"><?= e($message['message']) ?></div>
        <?php endforeach; ?>

        <?= $content ?>
    </div>
</div>

<script src="<?= asset('js/admin.js') ?>"></script>
<?php if (!empty($pageScript)): ?><script src="<?= asset('js/' . $pageScript) ?>"></script><?php endif; ?>
</body>
</html>
