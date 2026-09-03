<?php /** @var string|null $message */ ?>
<section class="page-hero page-hero--tall">
    <p class="eyebrow">404</p>
    <h1>This frame is missing</h1>
    <p class="page-hero__sub"><?= e($message ?? 'The page you are looking for is not here anymore.') ?></p>
    <div class="hero__cta">
        <a class="btn btn--primary" href="<?= url('/') ?>">Back home</a>
        <a class="btn btn--ghost" href="<?= url('/work') ?>">See the work</a>
    </div>
</section>
