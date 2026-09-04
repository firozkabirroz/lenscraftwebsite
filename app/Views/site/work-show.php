<?php /** @var array $project @var array $gallery @var array $related */ ?>
<section class="project-cover">
    <?php if ($project['cover_path']): ?>
        <img class="project-cover__image" src="<?= e(uploaded($project['cover_path'])) ?>" alt="<?= e($project['title']) ?>">
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

        <?php if ($gallery): ?>
            <h2>Stills</h2>
            <div class="gallery">
                <?php foreach ($gallery as $still): ?>
                    <figure style="background-image:url(<?= e(uploaded($still['path'])) ?>)">
                        <figcaption><?= e($still['title'] ?: $still['filename']) ?></figcaption>
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
                <a class="card-work" href="<?= url('/work/' . $item['slug']) ?>">
                    <div class="card-work__thumb" <?= $item['cover_path'] ? 'style="background-image:url(' . e(uploaded($item['cover_path'])) . ')"' : '' ?>></div>
                    <div class="card-work__body">
                        <h3><?= e($item['title']) ?></h3>
                        <span class="card-work__meta"><?= e((string) $item['year']) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
