<?php
/** @var array $sections */
$grouped = [];
foreach ($sections as $section) {
    $grouped[$section['page']][] = $section;
}
?>
<?php foreach ($grouped as $page => $items): ?>
    <article class="card">
        <header class="card__head">
            <h2><?= e(ucfirst($page)) ?> page</h2>
            <a class="link-arrow" href="<?= url($page === 'home' ? '/' : '/' . $page) ?>" target="_blank" rel="noopener">View page →</a>
        </header>
        <ul class="section-list">
            <?php foreach ($items as $section): ?>
                <li>
                    <div>
                        <strong><?= e($section['title']) ?></strong>
                        <small><?= e((string) $section['description']) ?></small>
                    </div>
                    <span class="muted">Updated <?= e(time_ago($section['updated_at'])) ?></span>
                    <a class="link" href="<?= url('/admin/content/' . $section['id'] . '/edit') ?>">Edit</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </article>
<?php endforeach; ?>

<?php if (!$sections): ?>
    <p class="empty">No content sections found. Import <code>database/seed.sql</code> to load the default site copy.</p>
<?php endif; ?>
