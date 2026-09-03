<?php /** @var array $videos @var array $stats @var string $status */ ?>
<div class="stat-row">
    <article class="card stat"><span class="stat__label">VIDEOS</span><strong class="stat__value"><?= number_format($stats['total']) ?></strong><span class="stat__meta"><?= number_format($stats['published']) ?> published</span></article>
    <article class="card stat"><span class="stat__label">TOTAL VIEWS</span><strong class="stat__value"><?= number_format($stats['views']) ?></strong><span class="stat__meta">across the site</span></article>
    <article class="card stat"><span class="stat__label">PROCESSING</span><strong class="stat__value"><?= number_format($stats['processing']) ?></strong><span class="stat__meta">encoding queue</span></article>
    <article class="card stat"><span class="stat__label">STORAGE</span><strong class="stat__value"><?= e(human_size($stats['storage'])) ?></strong><span class="stat__meta">uploaded files</span></article>
</div>

<div class="toolbar">
    <div class="chips">
        <?php foreach (['' => 'All', 'ready' => 'Ready', 'processing' => 'Processing', 'failed' => 'Failed'] as $key => $label): ?>
            <a class="chip <?= $status === $key ? 'is-active' : '' ?>" href="<?= url('/admin/videos') ?><?= $key ? '?status=' . $key : '' ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
    </div>
    <a class="btn btn--primary btn--sm" href="<?= url('/admin/videos/upload') ?>">Upload Video</a>
</div>

<article class="card card--flush">
    <table class="table table--wide">
        <thead>
        <tr><th>Video</th><th>Category</th><th>Source</th><th>Duration</th><th>Placement</th><th>Views</th><th>Status</th><th class="right">Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($videos as $video): ?>
            <tr>
                <td>
                    <div class="cell-media">
                        <span class="thumb thumb--video" <?= $video['poster_path'] ? 'style="background-image:url(' . e(uploaded($video['poster_path'])) . ')"' : '' ?>>▶</span>
                        <div>
                            <strong><?= e($video['title']) ?></strong>
                            <small><?= e($video['project_title'] ?: 'Not linked to a project') ?></small>
                        </div>
                    </div>
                </td>
                <td><?= e($video['category']) ?></td>
                <td><?= e($video['source'] === 'embed' ? ucfirst((string) $video['provider']) : 'Local file') ?></td>
                <td class="mono"><?= e(duration_format((int) $video['duration_seconds'])) ?></td>
                <td class="tags">
                    <?php if ($video['place_home_hero']): ?><span class="tag">Home hero</span><?php endif; ?>
                    <?php if ($video['place_work_grid']): ?><span class="tag">Work</span><?php endif; ?>
                    <?php if ($video['place_services']): ?><span class="tag">Services</span><?php endif; ?>
                </td>
                <td><?= number_format((int) $video['views']) ?></td>
                <td>
                    <span class="badge badge--<?= e(status_tone($video['status'])) ?>"><?= e(ucfirst($video['status'])) ?></span>
                    <?php if (!$video['is_published']): ?><span class="badge badge--muted">Hidden</span><?php endif; ?>
                </td>
                <td class="right actions">
                    <a class="link" href="<?= url('/admin/videos/' . $video['id'] . '/edit') ?>">Edit</a>
                    <form method="post" action="<?= url('/admin/videos/' . $video['id'] . '/delete') ?>" data-confirm="Delete this video?">
                        <?= csrf_field() ?>
                        <button class="link link--danger" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$videos): ?>
            <tr><td colspan="8" class="empty">No videos yet — upload the showreel to get started.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</article>
