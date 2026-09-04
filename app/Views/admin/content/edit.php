<?php /** @var array $section @var array $fields @var array $values @var array $revisions */ ?>
<form method="post" action="<?= url('/admin/content/' . $section['id']) ?>" class="cols cols--2-1 form">
    <?= csrf_field() ?>

    <div class="stack">
        <article class="card">
            <h2 class="card__label"><?= e(strtoupper($section['title'])) ?></h2>

            <?php foreach ($fields as $key => $meta): ?>
                <?php $type = $meta['type'] ?? 'text'; ?>

                <?php if ($type === 'textarea'): ?>
                    <label class="field">
                        <span><?= e($meta['label']) ?></span>
                        <textarea name="<?= e($key) ?>" rows="4"><?= e((string) ($values[$key] ?? '')) ?></textarea>
                    </label>

                <?php elseif ($type === 'select'): ?>
                    <label class="field">
                        <span><?= e($meta['label']) ?></span>
                        <select name="<?= e($key) ?>">
                            <?php foreach (($meta['options'] ?? []) as $optionValue => $optionLabel): ?>
                                <option value="<?= e((string) $optionValue) ?>" <?= ($values[$key] ?? '') === (string) $optionValue ? 'selected' : '' ?>>
                                    <?= e($optionLabel) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                <?php elseif ($type === 'toggle'): ?>
                    <label class="switch-row">
                        <span><?= e($meta['label']) ?></span>
                        <span class="switch"><input type="checkbox" name="<?= e($key) ?>" <?= ($values[$key] ?? '0') === '1' ? 'checked' : '' ?>><i></i></span>
                    </label>

                <?php elseif ($type === 'repeater'): ?>
                    <span class="field__label"><?= e($meta['label']) ?></span>
                    <div class="repeater" data-repeater>
                        <?php $rows = $values[$key] ?? []; ?>
                        <?php $rows = $rows ?: [array_fill_keys(array_keys($meta['fields']), '')]; ?>
                        <?php foreach ($rows as $row): ?>
                            <div class="repeater__row repeater__row--stack" data-repeater-row>
                                <div class="repeater__tools">
                                    <button type="button" class="link" data-repeater-up title="Move up">↑</button>
                                    <button type="button" class="link" data-repeater-down title="Move down">↓</button>
                                    <button type="button" class="link link--danger" data-repeater-remove>Remove</button>
                                </div>
                                <?php foreach ($meta['fields'] as $subKey => $subLabel): ?>
                                    <input type="text" name="<?= e($key . '_' . $subKey) ?>[]"
                                           value="<?= e((string) ($row[$subKey] ?? '')) ?>"
                                           placeholder="<?= e($subLabel) ?>">
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn--ghost btn--sm" data-repeater-add>Add item</button>

                <?php else: ?>
                    <label class="field">
                        <span><?= e($meta['label']) ?></span>
                        <input type="text" name="<?= e($key) ?>" value="<?= e((string) ($values[$key] ?? '')) ?>">
                    </label>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if (!$fields): ?>
                <p class="empty">This section has no editable fields defined yet.</p>
            <?php endif; ?>
        </article>
    </div>

    <div class="stack">
        <article class="card">
            <h2 class="card__label">PUBLISH</h2>
            <ul class="kv">
                <li><span>Page</span><strong><?= e(ucfirst($section['page'])) ?></strong></li>
                <li><span>Section key</span><strong class="mono"><?= e($section['section_key']) ?></strong></li>
                <li><span>Last updated</span><strong><?= e(time_ago($section['updated_at'])) ?></strong></li>
            </ul>
            <div class="btn-row">
                <button class="btn btn--primary btn--sm" type="submit">Save &amp; Publish</button>
                <a class="btn btn--ghost btn--sm" href="<?= url($section['page'] === 'home' ? '/' : '/' . $section['page']) ?>" target="_blank" rel="noopener">Preview</a>
            </div>
        </article>

        <article class="card">
            <h2 class="card__label">LIVE PREVIEW</h2>
            <div class="preview">
                <p class="preview__eyebrow"><?= e((string) ($values['brand_line'] ?? ucfirst($section['page']))) ?></p>
                <h3><?= e((string) ($values['tagline'] ?? $values['heading'] ?? $section['title'])) ?></h3>
                <p><?= e(excerpt((string) ($values['intro'] ?? $values['subheading'] ?? $values['body'] ?? ''), 160)) ?></p>
            </div>
        </article>
    </div>
</form>

<article class="card">
    <h2 class="card__label">VERSION HISTORY</h2>
    <ul class="section-list">
        <?php foreach ($revisions as $revision): ?>
            <li>
                <div>
                    <strong><?= e((string) $revision['note']) ?></strong>
                    <small><?= e($revision['user_name'] ?? 'System') ?> · <?= e(time_ago($revision['created_at'])) ?></small>
                </div>
                <form method="post" action="<?= url('/admin/content/' . $section['id'] . '/restore') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="revision_id" value="<?= (int) $revision['id'] ?>">
                    <button class="link" type="submit">Restore</button>
                </form>
            </li>
        <?php endforeach; ?>
        <?php if (!$revisions): ?><li class="empty">No earlier versions yet.</li><?php endif; ?>
    </ul>
</article>
