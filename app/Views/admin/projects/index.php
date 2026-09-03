<?php /** @var array $projects @var array $categories @var string $active */ ?>
<div class="toolbar">
    <div class="chips">
        <a class="chip <?= $active === '' ? 'is-active' : '' ?>" href="<?= url('/admin/projects') ?>">All</a>
        <?php foreach ($categories as $category): ?>
            <a class="chip <?= $active === $category ? 'is-active' : '' ?>" href="<?= url('/admin/projects') ?>?category=<?= urlencode($category) ?>">
                <?= e($category) ?>
            </a>
        <?php endforeach; ?>
    </div>
    <span class="muted"><?= count($projects) ?> projects · lowest order number shows first</span>
</div>

<article class="card card--flush">
    <table class="table table--wide">
        <thead>
        <tr><th>Order</th><th>Project</th><th>Category</th><th>Year</th><th>Status</th><th>Views</th><th class="right">Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($projects as $project): ?>
            <tr>
                <td class="mono"><?= str_pad((string) $project['sort_order'], 2, '0', STR_PAD_LEFT) ?></td>
                <td>
                    <div class="cell-media">
                        <span class="thumb" <?= $project['cover_path'] ? 'style="background-image:url(' . e(uploaded($project['cover_path'])) . ')"' : '' ?>></span>
                        <div>
                            <strong><?= e($project['title']) ?></strong>
                            <small><?= e($project['client_name'] ?: 'No client set') ?></small>
                        </div>
                    </div>
                </td>
                <td><?= e($project['category']) ?></td>
                <td><?= e((string) $project['year']) ?></td>
                <td><span class="badge badge--<?= e(status_tone($project['status'])) ?>"><?= e(ucfirst($project['status'])) ?></span></td>
                <td><?= number_format((int) $project['views']) ?></td>
                <td class="right actions">
                    <a class="link" href="<?= url('/admin/projects/' . $project['id'] . '/edit') ?>">Edit</a>
                    <a class="link link--muted" href="<?= url('/work/' . $project['slug']) ?>" target="_blank" rel="noopener">Preview</a>
                    <form method="post" action="<?= url('/admin/projects/' . $project['id'] . '/delete') ?>" data-confirm="Delete this project?">
                        <?= csrf_field() ?>
                        <button class="link link--danger" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$projects): ?>
            <tr><td colspan="7" class="empty">No projects match this filter.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</article>
