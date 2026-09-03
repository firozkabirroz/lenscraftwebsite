<?php /** @var array $items @var array $stats @var string $type */ ?>
<div class="toolbar">
    <div class="chips">
        <?php foreach (['' => 'All', 'image' => 'Images', 'doc' => 'Documents'] as $key => $label): ?>
            <a class="chip <?= $type === $key ? 'is-active' : '' ?>" href="<?= url('/admin/media') ?><?= $key ? '?type=' . $key : '' ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
    </div>
    <span class="muted"><?= number_format($stats['count']) ?> files · <?= e(human_size($stats['storage'])) ?></span>
</div>

<article class="card" id="upload">
    <h2 class="card__label">UPLOAD</h2>
    <form method="post" action="<?= url('/admin/media') ?>" enctype="multipart/form-data" class="media-upload">
        <?= csrf_field() ?>
        <input type="file" name="files[]" multiple accept="image/*,application/pdf" required>
        <button class="btn btn--primary btn--sm" type="submit">Upload files</button>
        <span class="muted">JPG, PNG, WebP, SVG or PDF · up to <?= e(human_size((int) config('uploads')['max_image'])) ?> each</span>
    </form>
</article>

<div class="media-grid">
    <?php foreach ($items as $item): ?>
        <figure class="media-card">
            <div class="media-card__thumb" style="background-image:url(<?= e(uploaded($item['path'])) ?>)">
                <?php if ($item['type'] === 'doc'): ?><span class="media-card__doc">PDF</span><?php endif; ?>
            </div>
            <figcaption>
                <strong><?= e($item['title'] ?: $item['filename']) ?></strong>
                <small><?= e($item['width'] ? $item['width'] . '×' . $item['height'] : strtoupper((string) $item['type'])) ?> · <?= e(human_size((int) $item['size_bytes'])) ?></small>
            </figcaption>
            <div class="media-card__actions">
                <a class="link" href="<?= e(uploaded($item['path'])) ?>" target="_blank" rel="noopener">Open</a>
                <form method="post" action="<?= url('/admin/media/' . $item['id'] . '/delete') ?>" data-confirm="Delete this file?">
                    <?= csrf_field() ?>
                    <button class="link link--danger" type="submit">Delete</button>
                </form>
            </div>
        </figure>
    <?php endforeach; ?>
    <?php if (!$items): ?>
        <p class="empty">Nothing in the library yet.</p>
    <?php endif; ?>
</div>
