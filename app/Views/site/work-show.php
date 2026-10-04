<?php
/** @var array $project @var array $gallery @var array $related */

// Challenge -> Approach -> Result. Any block the studio has not filled in is
// skipped rather than rendered empty, so older projects degrade to the plain
// description they already had.
$narrative = array_values(array_filter([
    ['label' => 'Challenge', 'body' => trim((string) ($project['challenge'] ?? ''))],
    ['label' => 'Approach',  'body' => trim((string) ($project['approach'] ?? ''))],
    ['label' => 'Result',    'body' => trim((string) ($project['outcome'] ?? ''))],
], static fn (array $part): bool => $part['body'] !== ''));

$quote = trim((string) ($project['quote_text'] ?? ''));

// Behind-the-scenes stills read as a considered set, not a dump of the
// library, so the page shows at most five.
$stills = array_slice($gallery, 0, 5);
?>
<section class="project-cover">
    <?php if ($project['cover_path']): ?>
        <img class="project-cover__image" src="<?= e(uploaded($project['cover_path'])) ?>" alt="<?= e($project['title']) ?>" fetchpriority="high">
    <?php else: ?>
        <div class="project-cover__placeholder"></div>
    <?php endif; ?>
</section>

<section class="section project-intro">
    <p class="eyebrow"><?= e($project['category']) ?> · <?= e((string) $project['year']) ?></p>
    <h1><?= e($project['title']) ?></h1>
    <?php if ($project['summary']): ?>
        <p class="project-intro__sub"><?= e($project['summary']) ?></p>
    <?php endif; ?>
</section>

<section class="section project-body">
    <div class="project-body__main">
        <?php if (!empty($project['hero_video_url'])): ?>
            <div class="video-frame">
                <iframe src="<?= e(embed_url($project['hero_video_url'])) ?>" title="<?= e($project['title']) ?>" allowfullscreen loading="lazy"></iframe>
            </div>
        <?php endif; ?>

        <h2>About the project</h2>
        <p class="prose"><?= nl2br(e($project['description'])) ?></p>

        <?php if ($narrative): ?>
            <div class="case-study">
                <?php foreach ($narrative as $i => $part): ?>
                    <article class="case-step" data-reveal>
                        <span class="case-step__no"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <div class="case-step__text">
                            <h2><?= e($part['label']) ?></h2>
                            <p class="prose"><?= nl2br(e($part['body'])) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($quote !== ''): ?>
            <figure class="quote-card" data-reveal>
                <blockquote><?= nl2br(e($quote)) ?></blockquote>
                <?php if (!empty($project['quote_author'])): ?>
                    <figcaption>
                        <span class="quote-card__author"><?= e($project['quote_author']) ?></span>
                        <?php if (!empty($project['quote_role'])): ?>
                            <span class="quote-card__role"><?= e($project['quote_role']) ?></span>
                        <?php endif; ?>
                    </figcaption>
                <?php endif; ?>
            </figure>
        <?php endif; ?>

        <?php if ($stills): ?>
            <h2>Stills</h2>
            <div class="gallery">
                <?php foreach ($stills as $still): ?>
                    <figure data-reveal>
                        <img src="<?= e(uploaded($still['path'])) ?>"
                             alt="<?= e($still['alt'] ?: ($still['title'] ?: $project['title'])) ?>"
                             <?= (int) ($still['width'] ?? 0) > 0 ? 'width="' . (int) $still['width'] . '" height="' . (int) $still['height'] . '"' : '' ?>
                             loading="lazy" decoding="async">
                        <?php if ($still['title'] ?: $still['filename']): ?>
                            <figcaption><?= e($still['title'] ?: $still['filename']) ?></figcaption>
                        <?php endif; ?>
                    </figure>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <aside class="project-body__side">
        <div class="fact-card">
            <h3>Project facts</h3>
            <dl>
                <dt>Client</dt><dd><?= e($project['client_name'] ?: '—') ?></dd>
                <dt>Category</dt><dd><?= e($project['category']) ?></dd>
                <dt>Year</dt><dd><?= e((string) $project['year']) ?></dd>
                <dt>Views</dt><dd><?= number_format((int) $project['views']) ?></dd>
            </dl>
            <a class="btn btn--primary btn--block" href="<?= url('/contact') ?>">Brief a similar project</a>
        </div>
    </aside>
</section>

<?php if ($related): ?>
    <section class="section section--alt">
        <div class="section__head">
            <h2>More <?= e($project['category']) ?></h2>
            <a class="link-arrow" href="<?= url('/work') ?>">All work →</a>
        </div>
        <div class="grid grid--work">
            <?php foreach ($related as $item): ?>
                <a class="card-work" href="<?= url('/work/' . $item['slug']) ?>"<?= preview_attrs($item['hero_video_url'] ?? '', $item['preview_video_path'] ?? null) ?>>
                    <?= work_thumb($item) ?>
                    <div class="card-work__body">
                        <h3><?= e($item['title']) ?></h3>
                        <span class="card-work__meta"><?= e((string) $item['year']) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
