<?php /** @var array $vision @var array $approach @var array $projects */ ?>
<section class="page-hero">
    <p class="eyebrow">Studio</p>
    <h1><?= e($hero['heading'] ?? 'About LensCraft') ?></h1>
    <p class="page-hero__sub"><?= e($hero['subheading'] ?? '') ?></p>
</section>

<section class="section two-col">
    <article class="panel">
        <p class="eyebrow">Vision</p>
        <p class="lead"><?= e($vision['vision'] ?? '') ?></p>
    </article>
    <article class="panel">
        <p class="eyebrow">Mission</p>
        <p class="lead"><?= e($vision['mission'] ?? '') ?></p>
    </article>
</section>

<section class="section section--alt">
    <div class="section__head">
        <div>
            <p class="eyebrow">How we work</p>
            <h2>Approach</h2>
        </div>
    </div>
    <div class="grid grid--steps">
        <?php foreach (($approach['steps'] ?? []) as $i => $step): ?>
            <article class="step">
                <span class="step__no"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                <h3><?= e($step['title'] ?? '') ?></h3>
                <p><?= e($step['desc'] ?? '') ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php if ($projects): ?>
    <section class="section">
        <div class="section__head">
            <h2>Recent films</h2>
            <a class="link-arrow" href="<?= url('/work') ?>">All work →</a>
        </div>
        <div class="grid grid--work">
            <?php foreach ($projects as $project): ?>
                <a class="card-work" href="<?= url('/work/' . $project['slug']) ?>">
                    <div class="card-work__thumb" <?= $project['cover_path'] ? 'style="background-image:url(' . e(uploaded($project['cover_path'])) . ')"' : '' ?>></div>
                    <div class="card-work__body">
                        <h3><?= e($project['title']) ?></h3>
                        <span class="card-work__meta"><?= e($project['category']) ?> · <?= e((string) $project['year']) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<section class="cta-band">
    <div>
        <h2><?= e($ctaBand['heading'] ?? 'Work with the crew') ?></h2>
        <p><?= e($ctaBand['body'] ?? '') ?></p>
    </div>
    <a class="btn btn--primary" href="<?= url('/contact') ?>"><?= e($ctaBand['cta'] ?? 'Start a Project') ?></a>
</section>
