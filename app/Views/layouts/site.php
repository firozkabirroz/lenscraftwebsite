<?php
/** @var string $content */
$settings = $settings ?? [];
$studio = $settings['studio_name'] ?? 'LensCraft Production';
$pageTitle = isset($title) && $title ? $title . ' — ' . $studio : $studio . ' — ' . ($settings['tagline'] ?? '');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($settings['meta_description'] ?? '') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/site.css') ?>">
    <link rel="icon" href="<?= asset('img/logo.svg') ?>" type="image/svg+xml">
</head>
<body>
<div class="preloader" id="preloader" aria-hidden="true">
    <img src="<?= asset('img/logo.svg') ?>" alt="" width="52" height="52">
    <span><?= e(strtoupper($studio)) ?></span>
</div>

<header class="nav" id="siteNav">
    <div class="nav__inner">
        <a class="nav__brand" href="<?= url('/') ?>">
            <img src="<?= asset('img/logo.svg') ?>" alt="" width="34" height="34">
            <span><?= e($studio) ?></span>
        </a>
        <nav class="nav__links" id="navLinks">
            <a href="<?= url('/') ?>" class="<?= current_path() === '/' ? 'is-active' : '' ?>">Home</a>
            <a href="<?= url('/work') ?>" class="<?= is_active('/work') ? 'is-active' : '' ?>">Work</a>
            <a href="<?= url('/services') ?>" class="<?= is_active('/services') ? 'is-active' : '' ?>">Services</a>
            <a href="<?= url('/about') ?>" class="<?= is_active('/about') ? 'is-active' : '' ?>">About</a>
            <a href="<?= url('/contact') ?>" class="<?= is_active('/contact') ? 'is-active' : '' ?>">Contact</a>
        </nav>
        <div class="nav__actions">
            <a class="btn btn--primary btn--sm" href="<?= url('/contact') ?>">Start a Project</a>
            <button class="nav__toggle" id="navToggle" aria-label="Menu">☰</button>
        </div>
    </div>
</header>

<main>
<?= $content ?>
</main>

<?php if (current_path() !== '/contact'): ?>
    <a class="footer-cta" href="<?= url('/contact') ?>">
        <span class="footer-cta__label">Have a project in mind?</span>
        <span class="footer-cta__title">Start a Project <em>→</em></span>
    </a>
<?php endif; ?>

<footer class="footer">
    <div class="footer__inner">
        <div class="footer__brand">
            <img src="<?= asset('img/logo.svg') ?>" alt="" width="40" height="40">
            <p class="footer__tag"><?= e($settings['tagline'] ?? '') ?></p>
            <p class="footer__note"><?= e($settings['footer_note'] ?? '') ?></p>
        </div>
        <div class="footer__col">
            <h4>Pages</h4>
            <a href="<?= url('/') ?>">Home</a>
            <a href="<?= url('/work') ?>">Work</a>
            <a href="<?= url('/services') ?>">Services</a>
            <a href="<?= url('/about') ?>">About</a>
            <a href="<?= url('/contact') ?>">Contact</a>
        </div>
        <div class="footer__col">
            <h4>Studio</h4>
            <a href="tel:<?= e(str_replace(' ', '', $settings['phone'] ?? '')) ?>"><?= e($settings['phone'] ?? '') ?></a>
            <a href="mailto:<?= e($settings['email'] ?? '') ?>"><?= e($settings['email'] ?? '') ?></a>
            <span><?= e($settings['address'] ?? '') ?></span>
            <span><?= e($settings['hours'] ?? '') ?></span>
        </div>
        <div class="footer__col">
            <h4>Follow</h4>
            <?php if (!empty($settings['instagram'])): ?><a href="<?= e($settings['instagram']) ?>" target="_blank" rel="noopener">Instagram</a><?php endif; ?>
            <?php if (!empty($settings['youtube'])): ?><a href="<?= e($settings['youtube']) ?>" target="_blank" rel="noopener">YouTube</a><?php endif; ?>
            <?php if (!empty($settings['facebook'])): ?><a href="<?= e($settings['facebook']) ?>" target="_blank" rel="noopener">Facebook</a><?php endif; ?>
            <a href="<?= url('/admin') ?>">Studio Login</a>
        </div>
    </div>
    <div class="footer__base">
        <span>© <?= date('Y') ?> <?= e($studio) ?>. All rights reserved.</span>
        <span>Dhaka, Bangladesh</span>
    </div>
</footer>

<script src="<?= asset('js/site.js') ?>"></script>
</body>
</html>
