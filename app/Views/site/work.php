<?php /** @var array $projects @var array $categories @var string $active */ ?>
<section class="page-hero">
    <p class="eyebrow">Portfolio</p>
    <h1><?= e($hero['heading'] ?? 'Work') ?></h1>
    <p class="page-hero__sub"><?= e($hero['subheading'] ?? '') ?></p>
</section>

<section class="section">
    <div class="filters">
        <a class="chip <?= $active === '' ? 'is-active' : '' ?>" href="<?= url('/work') ?>">All</a>
        <?php foreach ($categories as $category): ?>
            <a class="chip <?= $active === $category ? 'is-active' : '' ?>" href="<?= url('/work') ?>?category=<?= urlencode($category) ?>">
                <?= e($category) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="grid grid--work">
        <?php foreach ($projects as $project): ?>
            <a class="card-work" href="<?= url('/work/' . $project['slug']) ?>"<?= preview_attrs($project['hero_video_url'] ?? '') ?>>
                <div class="card-work__thumb" <?= $project['cover_path'] ? 'style="background-image:url(' . e(uploaded($project['cover_path'])) . ')"' : '' ?>>
                    <span class="card-work__cat"><?= e($project['category']) ?></span>
                </div>
                <div class="card-work__body">
                    <h3><?= e($project['title']) ?></h3>
                    <p><?= e($project['summary'] ?: excerpt($project['description'], 90)) ?></p>
                    <span class="card-work__meta"><?= e($project['client_name'] ?: 'LensCraft') ?> · <?= e((string) $project['year']) ?></span>
                </div>
            </a>
        <?php endforeach; ?>
        <?php if (!$projects): ?>
            <p class="empty">No projects in this category yet.</p>
        <?php endif; ?>
    </div>
</section>

<section class="cta-band">
    <div>
        <h2>Planning a film?</h2>
        <p>Send the brief and we will come back with an approach, crew and a quote.</p>
    </div>
    <a class="btn btn--primary" href="<?= url('/contact') ?>">Start a Project</a>
</section>
