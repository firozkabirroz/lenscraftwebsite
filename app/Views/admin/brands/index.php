<?php /** @var array $brands @var array|null $editing @var array $images */ ?>
<div class="cols cols--2-1">
    <article class="card card--flush">
        <table class="table table--wide">
            <thead>
            <tr><th>Order</th><th>Brand</th><th>Logo</th><th>Website</th><th>Status</th><th class="right">Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($brands as $brand): ?>
                <tr>
                    <td>
                        <div class="order-controls">
                            <form method="post" action="<?= url('/admin/brands/' . $brand['id'] . '/move') ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="direction" value="up">
                                <button class="link" type="submit" title="Move up">↑</button>
                            </form>
                            <span class="mono"><?= str_pad((string) $brand['sort_order'], 2, '0', STR_PAD_LEFT) ?></span>
                            <form method="post" action="<?= url('/admin/brands/' . $brand['id'] . '/move') ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="direction" value="down">
                                <button class="link" type="submit" title="Move down">↓</button>
                            </form>
                        </div>
                    </td>
                    <td><strong><?= e($brand['name']) ?></strong></td>
                    <td>
                        <?php if ($brand['logo_path']): ?>
                            <span class="thumb thumb--logo" style="background-image:url(<?= e(uploaded($brand['logo_path'])) ?>)"></span>
                        <?php else: ?>
                            <span class="muted">Text only</span>
                        <?php endif; ?>
                    </td>
                    <td class="muted"><?= e($brand['website'] ?: '—') ?></td>
                    <td><span class="badge badge--<?= $brand['is_active'] ? 'success' : 'muted' ?>"><?= $brand['is_active'] ? 'Visible' : 'Hidden' ?></span></td>
                    <td class="right actions">
                        <a class="link" href="<?= url('/admin/brands') ?>?edit=<?= (int) $brand['id'] ?>#brand-form">Edit</a>
                        <form method="post" action="<?= url('/admin/brands/' . $brand['id'] . '/delete') ?>" data-confirm="Remove this brand from the strip?">
                            <?= csrf_field() ?>
                            <button class="link link--danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$brands): ?>
                <tr><td colspan="6" class="empty">No brands yet — add the first one on the right.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </article>

    <article class="card" id="brand-form">
        <h2 class="card__label"><?= $editing ? 'EDIT BRAND' : 'ADD BRAND' ?></h2>
        <form method="post" action="<?= $editing ? url('/admin/brands/' . $editing['id']) : url('/admin/brands') ?>">
            <?= csrf_field() ?>
            <label class="field"><span>Brand name *</span><input type="text" name="name" value="<?= e((string) ($editing['name'] ?? '')) ?>" required></label>
            <div class="field-row">
                <label class="field"><span>Website</span><input type="url" name="website" value="<?= e((string) ($editing['website'] ?? '')) ?>" placeholder="https://"></label>
                <label class="field"><span>Order</span><input type="number" name="sort_order" value="<?= e((string) ($editing['sort_order'] ?? '')) ?>"></label>
            </div>

            <span class="field__label">Logo (upload it in <a class="link" href="<?= url('/admin/media') ?>">Media</a> first — SVG or PNG on transparent works best)</span>
            <div class="picker">
                <label class="picker__item <?= empty($editing['logo_media_id']) ? 'is-selected' : '' ?>">
                    <input type="radio" name="logo_media_id" value="" <?= empty($editing['logo_media_id']) ? 'checked' : '' ?>>
                    <span class="picker__none">Text only</span>
                </label>
                <?php foreach ($images as $image): ?>
                    <label class="picker__item <?= (int) ($editing['logo_media_id'] ?? 0) === (int) $image['id'] ? 'is-selected' : '' ?>"
                           style="background-image:url(<?= e(uploaded($image['path'])) ?>); background-size: contain; background-repeat: no-repeat; background-position: center;">
                        <input type="radio" name="logo_media_id" value="<?= (int) $image['id'] ?>" <?= (int) ($editing['logo_media_id'] ?? 0) === (int) $image['id'] ? 'checked' : '' ?>>
                        <span class="picker__name"><?= e($image['filename']) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>

            <label class="switch-row">
                <span>Show on the site</span>
                <span class="switch"><input type="checkbox" name="is_active" <?= !isset($editing) || $editing === null || !empty($editing['is_active']) ? 'checked' : '' ?>><i></i></span>
            </label>

            <div class="btn-row">
                <button class="btn btn--primary btn--sm" type="submit"><?= $editing ? 'Save Brand' : 'Add Brand' ?></button>
                <?php if ($editing): ?><a class="btn btn--ghost btn--sm" href="<?= url('/admin/brands') ?>">Cancel</a><?php endif; ?>
            </div>
        </form>
        <p class="muted">The strip heading is editable in <a class="link" href="<?= url('/admin/content') ?>">Content → Brand Strip</a>.</p>
    </article>
</div>
