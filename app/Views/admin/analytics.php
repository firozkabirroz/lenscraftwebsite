<?php
/** @var array $summary @var array $weekly @var array $sources @var array $topVideos @var array $bookingTypes */
$maxWeekly = max(array_merge([1], array_map('intval', array_values($weekly))));
$totalSources = max(1, array_sum(array_column($sources, 'total')));
$maxType = max(array_merge([1], array_map('intval', array_column($bookingTypes, 'total'))));
?>
<div class="stat-row">
    <article class="card stat"><span class="stat__label">SITE VISITS · 30D</span><strong class="stat__value"><?= number_format($summary['visits']) ?></strong><span class="stat__meta"><?= $summary['visits_change'] >= 0 ? '+' : '' ?><?= e((string) $summary['visits_change']) ?>% vs previous</span></article>
    <article class="card stat"><span class="stat__label">VIDEO VIEWS</span><strong class="stat__value"><?= number_format($summary['video_views']) ?></strong><span class="stat__meta">all time</span></article>
    <article class="card stat"><span class="stat__label">NEW BOOKINGS</span><strong class="stat__value"><?= number_format($summary['bookings']) ?></strong><span class="stat__meta">last 30 days</span></article>
    <article class="card stat"><span class="stat__label">INQUIRY RATE</span><strong class="stat__value"><?= e((string) $summary['booking_rate']) ?>%</strong><span class="stat__meta">visits that became inquiries</span></article>
</div>

<div class="cols cols--2-1">
    <article class="card">
        <header class="card__head">
            <h2>Video plays per week</h2>
            <span class="muted">last <?= count($weekly) ?> weeks</span>
        </header>
        <div class="chart">
            <?php foreach ($weekly as $label => $value): ?>
                <div class="chart__bar" title="<?= e($label) ?>: <?= (int) $value ?>">
                    <span style="height: <?= max(2, (int) round($value / $maxWeekly * 100)) ?>%"></span>
                    <small><?= e($label) ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    </article>

    <article class="card">
        <header class="card__head"><h2>Top videos</h2></header>
        <ul class="rank">
            <?php foreach ($topVideos as $i => $video): ?>
                <li>
                    <span class="rank__no"><?= $i + 1 ?></span>
                    <div>
                        <strong><?= e($video['title']) ?></strong>
                        <small><?= number_format((int) $video['views']) ?> views</small>
                    </div>
                </li>
            <?php endforeach; ?>
            <?php if (!$topVideos): ?><li class="empty">No plays recorded yet.</li><?php endif; ?>
        </ul>
    </article>
</div>

<div class="cols cols--1-1">
    <article class="card">
        <header class="card__head"><h2>Traffic sources</h2></header>
        <ul class="meter">
            <?php foreach ($sources as $source): ?>
                <li>
                    <span><?= e(ucfirst((string) $source['source'])) ?></span>
                    <i><b style="width: <?= (int) round($source['total'] / $totalSources * 100) ?>%"></b></i>
                    <strong><?= (int) round($source['total'] / $totalSources * 100) ?>%</strong>
                </li>
            <?php endforeach; ?>
            <?php if (!$sources): ?><li class="empty">No traffic recorded yet.</li><?php endif; ?>
        </ul>
    </article>

    <article class="card">
        <header class="card__head"><h2>Bookings by type</h2></header>
        <ul class="meter">
            <?php foreach ($bookingTypes as $row): ?>
                <li>
                    <span><?= e($row['project_type']) ?></span>
                    <i><b style="width: <?= (int) round($row['total'] / $maxType * 100) ?>%"></b></i>
                    <strong><?= (int) $row['total'] ?></strong>
                </li>
            <?php endforeach; ?>
            <?php if (!$bookingTypes): ?><li class="empty">No bookings yet.</li><?php endif; ?>
        </ul>
    </article>
</div>

<article class="card card--flush">
    <header class="card__head card__head--pad"><h2>Most visited pages</h2></header>
    <table class="table">
        <thead><tr><th>Path</th><th class="right">Views</th></tr></thead>
        <tbody>
        <?php foreach ($topPages as $page): ?>
            <tr><td class="mono"><?= e($page['path']) ?></td><td class="right"><?= number_format((int) $page['total']) ?></td></tr>
        <?php endforeach; ?>
        <?php if (!$topPages): ?><tr><td colspan="2" class="empty">Traffic will appear here once the site gets visits.</td></tr><?php endif; ?>
        </tbody>
    </table>
</article>
