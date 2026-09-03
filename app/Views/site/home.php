<?php
/** @var array $hero @var array $projects @var array|null $reel */
$hero = $hero ?: [];
$dim = ($hero['dim_overlay'] ?? '1') === '1';
?>
<section class="hero <?= $dim ? 'hero--dim' : '' ?>">
    <div class="hero__bg" data-mode="<?= e($hero['background_mode'] ?? 'gradient') ?>">
        <?php if (($hero['background_mode'] ?? '') === 'video' && $reel): ?>
            <?php if ($reel['source'] === 'local' && $reel['file_path']): ?>
                <video class="hero__video" src="<?= e(uploaded($reel['file_path'])) ?>"
                       autoplay muted loop playsinline preload="metadata"
                       <?= $reel['poster_path'] ? 'poster="' . e(uploaded($reel['poster_path'])) . '"' : '' ?>></video>
            <?php elseif (!empty($reel['embed_url'])): ?>
                <?php
                $heroSrc = embed_provider($reel['embed_url']) === 'youtube'
                    ? embed_src($reel['embed_url'], [
                        'autoplay' => 1, 'mute' => 1, 'controls' => 0, 'loop' => 1,
                        'playlist' => basename((string) parse_url(embed_url($reel['embed_url']), PHP_URL_PATH)),
                        'modestbranding' => 1, 'playsinline' => 1,
                    ])
                    : embed_src($reel['embed_url'], [
                        'background' => 1, 'autoplay' => 1, 'muted' => 1, 'loop' => 1, 'autopause' => 0,
                        'title' => 0, 'byline' => 0, 'portrait' => 0,
                    ]);
                ?>
                <iframe class="hero__video hero__video--embed" src="<?= e($heroSrc) ?>"
                        title="Showreel" allow="autoplay; fullscreen; encrypted-media"
                        referrerpolicy="strict-origin-when-cross-origin"
                        tabindex="-1"></iframe>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div class="hero__inner">
        <p class="eyebrow"><?= e($hero['brand_line'] ?? 'LensCraft Production') ?></p>
        <h1 class="hero__title"><?= e($hero['tagline'] ?? 'Crafting Stories with Vision & Precision') ?></h1>
        <p class="hero__intro"><?= e($hero['intro'] ?? '') ?></p>
        <div class="hero__cta">
            <a class="btn btn--primary" href="<?= url($hero['primary_link'] ?? '/work') ?>"><?= e($hero['primary_cta'] ?? 'View Work') ?></a>
            <?php if ($reel): ?>
                <button class="btn btn--ghost" data-reel="<?= (int) $reel['id'] ?>"
                        data-track="<?= url('/api/video-view/' . (int) $reel['id']) ?>"
                        data-embed="<?= e($reel['source'] === 'embed' ? embed_url($reel['embed_url']) : uploaded($reel['file_path'])) ?>"
                        data-type="<?= e($reel['source']) ?>">
                    <?= e($hero['secondary_cta'] ?? 'Watch Reel') ?>
                </button>
            <?php endif; ?>
        </div>
    </div>
    <?php if (($hero['show_scroll_hint'] ?? '1') === '1'): ?>
        <span class="hero__scroll">Scroll</span>
    <?php endif; ?>
</section>

<?php if (($brandStrip['enabled'] ?? '1') === '1' && $brands): ?>
    <section class="brand-strip" aria-label="Brands we have worked with">
        <p class="brand-strip__label"><?= e($brandStrip['heading'] ?? 'Trusted by brands & broadcasters') ?></p>
        <div class="brand-strip__track">
            <?php foreach ($brands as $brand): ?>
                <?php $tag = $brand['website'] ? 'a' : 'span'; ?>
                <<?= $tag ?> class="brand-strip__item"
                    <?= $brand['website'] ? 'href="' . e($brand['website']) . '" target="_blank" rel="noopener"' : '' ?>
                    title="<?= e($brand['name']) ?>">
                    <?php if ($brand['logo_path']): ?>
                        <img src="<?= e(uploaded($brand['logo_path'])) ?>" alt="<?= e($brand['name']) ?>" loading="lazy">
                    <?php else: ?>
                        <em><?= e($brand['name']) ?></em>
                    <?php endif; ?>
                </<?= $tag ?>>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<section class="section" id="work">
    <div class="section__head">
        <div>
            <p class="eyebrow">Selected Work</p>
            <h2><?= e($selected['heading'] ?? 'Selected Work') ?></h2>
            <p class="section__sub"><?= e($selected['subheading'] ?? '') ?></p>
        </div>
        <a class="link-arrow" href="<?= url('/work') ?>">View all work →</a>
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
            <p class="empty">No published projects yet. Add one from the admin panel.</p>
        <?php endif; ?>
    </div>
</section>

<section class="section section--alt">
    <div class="section__head">
        <div>
            <p class="eyebrow">What we do</p>
            <h2><?= e($disciplines['heading'] ?? 'Disciplines') ?></h2>
        </div>
        <a class="link-arrow" href="<?= url('/services') ?>">All services →</a>
    </div>
    <div class="grid grid--disciplines">
        <?php foreach (($disciplines['items'] ?? []) as $i => $item): ?>
            <article class="card-discipline">
                <span class="card-discipline__no"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                <h3><?= e($item['title'] ?? '') ?></h3>
                <p><?= e($item['desc'] ?? '') ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="section about-teaser">
    <div class="about-teaser__text">
        <p class="eyebrow">The studio</p>
        <h2><?= e($aboutTeaser['heading'] ?? '') ?></h2>
        <p><?= e($aboutTeaser['body'] ?? '') ?></p>
        <a class="link-arrow" href="<?= url('/about') ?>"><?= e($aboutTeaser['cta'] ?? 'About the studio') ?> →</a>
    </div>
    <div class="about-teaser__stats">
        <div><strong>10+</strong><span>Years behind the camera</span></div>
        <div><strong>180+</strong><span>Films delivered</span></div>
        <div><strong>40+</strong><span>Brands & broadcasters</span></div>
        <div><strong>4K/6K</strong><span>Capture & finishing</span></div>
    </div>
</section>

<section class="cta-band">
    <div>
        <h2><?= e($ctaBand['heading'] ?? 'Have a story worth filming?') ?></h2>
        <p><?= e($ctaBand['body'] ?? '') ?></p>
    </div>
    <a class="btn btn--primary" href="<?= url('/contact') ?>"><?= e($ctaBand['cta'] ?? 'Start a Project') ?></a>
</section>

<div class="reel-modal" id="reelModal" hidden>
    <button class="reel-modal__close" id="reelClose" aria-label="Close">✕</button>
    <div class="reel-modal__frame" id="reelFrame"></div>
</div>
