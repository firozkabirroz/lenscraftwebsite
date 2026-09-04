<?php /** @var string $content */ ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login — LensCraft Production</title>
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="<?= asset('css/site.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <link rel="icon" href="<?= asset('img/logo.svg') ?>" type="image/svg+xml">
</head>
<body class="auth">
<div class="auth__wrap">
    <?php foreach (flash_all() as $message): ?>
        <div class="flash flash--<?= e($message['type']) ?>"><?= e($message['message']) ?></div>
    <?php endforeach; ?>
    <?= $content ?>
</div>
</body>
</html>
