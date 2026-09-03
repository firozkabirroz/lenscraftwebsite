<?php /** @var array $entries @var array $filters @var string $filter */ ?>
<div class="toolbar">
    <div class="chips">
        <?php foreach ($filters as $key => $label): ?>
            <a class="chip <?= $filter === (string) $key ? 'is-active' : '' ?>" href="<?= url('/admin/activity') ?><?= $key ? '?type=' . e((string) $key) : '' ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
    </div>
    <span class="muted"><?= count($entries) ?> entries</span>
</div>

<article class="card card--flush">
    <table class="table table--wide">
        <thead><tr><th>When</th><th>Who</th><th>Action</th><th>Target</th><th>Detail</th></tr></thead>
        <tbody>
        <?php foreach ($entries as $entry): ?>
            <tr>
                <td><?= e(time_ago($entry['created_at'])) ?></td>
                <td><strong><?= e($entry['user_name'] ?? 'System') ?></strong></td>
                <td><?= e($entry['action']) ?></td>
                <td><span class="tag"><?= e((string) ($entry['target_type'] ?: '—')) ?></span></td>
                <td class="muted"><?= e((string) $entry['meta']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$entries): ?><tr><td colspan="5" class="empty">Nothing logged for this filter.</td></tr><?php endif; ?>
        </tbody>
    </table>
</article>
