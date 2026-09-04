<?php /** @var array $services @var array $packageIntro @var array $packages */ ?>
<section class="page-hero">
    <p class="eyebrow">Capabilities</p>
    <h1><?= e($hero['heading'] ?? 'Services') ?></h1>
    <p class="page-hero__sub"><?= e($hero['subheading'] ?? '') ?></p>
</section>

<?php if (($packageIntro['enabled'] ?? '1') === '1' && $packages): ?>
    <section class="section section--alt" id="packages">
        <div class="section__head">
            <div>
                <p class="eyebrow">Packages</p>
                <h2><?= e($packageIntro['heading'] ?? 'Production packages') ?></h2>
                <p class="section__sub"><?= e($packageIntro['subheading'] ?? '') ?></p>
            </div>
        </div>

        <div class="package-grid">
            <?php foreach ($packages as $package): ?>
                <article class="package-card <?= $package['is_featured'] ? 'package-card--featured' : '' ?>">
                    <?php if ($package['is_featured']): ?>
                        <span class="package-card__badge">Most booked</span>
                    <?php endif; ?>
                    <div class="package-card__head">
                        <h3><?= e($package['name']) ?></h3>
                        <?php if ($package['tagline']): ?>
                            <p class="package-card__tagline"><?= e($package['tagline']) ?></p>
                        <?php endif; ?>
                    </div>
                    <?php if ($package['description']): ?>
                        <p class="package-card__desc"><?= e($package['description']) ?></p>
                    <?php endif; ?>
                    <?php if ($package['features']): ?>
                        <ul class="package-card__features">
                            <?php foreach ($package['features'] as $feature): ?>
                                <li><?= e($feature) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <a class="btn <?= $package['is_featured'] ? 'btn--primary' : 'btn--ghost' ?> btn--block package-card__cta"
                       href="<?= url('/contact') ?>?package=<?= e($package['slug']) ?>">
                        <?= e($package['cta_label'] ?: 'Enquire') ?>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<section class="section">
    <div class="section__head">
        <div>
            <p class="eyebrow">What we do</p>
            <h2>Full capabilities</h2>
            <p class="section__sub">Every package can be extended with extra shoot days, aerial coverage, or post services.</p>
        </div>
    </div>
    <div class="service-list">
        <?php foreach (($services['items'] ?? []) as $i => $service): ?>
            <article class="service">
                <span class="service__no"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                <div class="service__text">
                    <h2><?= e($service['title'] ?? '') ?></h2>
                    <p><?= e($service['desc'] ?? '') ?></p>
                    <?php if (!empty($service['bullets']) && is_array($service['bullets'])): ?>
                        <ul>
                            <?php foreach ($service['bullets'] as $bullet): ?>
                                <li><?= e((string) $bullet) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <a class="service__cta" href="<?= url('/contact') ?>?type=<?= urlencode($service['title'] ?? '') ?>">Enquire →</a>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="cta-band">
    <div>
        <h2><?= e($ctaBand['heading'] ?? 'Have a story worth filming?') ?></h2>
        <p><?= e($ctaBand['body'] ?? '') ?></p>
    </div>
    <a class="btn btn--primary" href="<?= url('/contact') ?>"><?= e($ctaBand['cta'] ?? 'Start a Project') ?></a>
</section>
