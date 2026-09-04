<?php /** @var array $packages @var array|null $editing @var array $types */ ?>
<div class="cols cols--2-1">
    <article class="card card--flush">
        <table class="table table--wide">
            <thead>
            <tr><th>Order</th><th>Package</th><th>Price (admin)</th><th>Type</th><th>Status</th><th class="right">Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($packages as $package): ?>
                <tr>
                    <td>
                        <div class="order-controls">
                            <form method="post" action="<?= url('/admin/packages/' . $package['id'] . '/move') ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="direction" value="up">
                                <button class="link" type="submit" title="Move up">↑</button>
                            </form>
                            <span class="mono"><?= str_pad((string) $package['sort_order'], 2, '0', STR_PAD_LEFT) ?></span>
                            <form method="post" action="<?= url('/admin/packages/' . $package['id'] . '/move') ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="direction" value="down">
                                <button class="link" type="submit" title="Move down">↓</button>
                            </form>
                        </div>
                    </td>
                    <td>
                        <strong><?= e($package['name']) ?></strong>
                        <?php if ($package['is_featured']): ?><span class="tag">Featured</span><?php endif; ?>
                        <small class="muted"><?= e($package['tagline'] ?: $package['slug']) ?></small>
                    </td>
                    <td class="mono"><?= e(($package['price_label'] ?: 'From') . ' ' . money((float) $package['price_from'], (string) $package['currency'])) ?></td>
                    <td><?= e($package['service_type'] ?: '—') ?></td>
                    <td><span class="badge badge--<?= $package['is_active'] ? 'success' : 'muted' ?>"><?= $package['is_active'] ? 'Visible' : 'Hidden' ?></span></td>
                    <td class="right actions">
                        <a class="link" href="<?= url('/admin/packages') ?>?edit=<?= (int) $package['id'] ?>#package-form">Edit</a>
                        <form method="post" action="<?= url('/admin/packages/' . $package['id'] . '/delete') ?>" data-confirm="Remove this package?">
                            <?= csrf_field() ?>
                            <button class="link link--danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$packages): ?>
                <tr><td colspan="6" class="empty">No packages yet — add the first one on the right.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </article>

    <article class="card" id="package-form">
        <h2 class="card__label"><?= $editing ? 'EDIT PACKAGE' : 'ADD PACKAGE' ?></h2>
        <form method="post" action="<?= $editing ? url('/admin/packages/' . $editing['id']) : url('/admin/packages') ?>">
            <?= csrf_field() ?>
            <label class="field"><span>Package name *</span><input type="text" name="name" value="<?= e((string) ($editing['name'] ?? '')) ?>" required></label>
            <div class="field-row">
                <label class="field"><span>URL slug</span><input type="text" name="slug" value="<?= e((string) ($editing['slug'] ?? '')) ?>" placeholder="auto-generated"></label>
                <label class="field"><span>Order</span><input type="number" name="sort_order" value="<?= e((string) ($editing['sort_order'] ?? '')) ?>"></label>
            </div>
            <label class="field"><span>Tagline</span><input type="text" name="tagline" value="<?= e((string) ($editing['tagline'] ?? '')) ?>" placeholder="Short line under the title"></label>
            <label class="field"><span>Description</span><textarea name="description" rows="3"><?= e((string) ($editing['description'] ?? '')) ?></textarea></label>

            <div class="field-row">
                <label class="field"><span>Price from</span><input type="text" name="price_from" value="<?= e((string) ((int) ($editing['price_from'] ?? 0))) ?>" placeholder="450000"></label>
                <label class="field"><span>Price label</span><input type="text" name="price_label" value="<?= e((string) ($editing['price_label'] ?? 'From')) ?>" placeholder="From"></label>
            </div>
            <div class="field-row">
                <label class="field"><span>Currency</span><input type="text" name="currency" value="<?= e((string) ($editing['currency'] ?? 'BDT')) ?>" maxlength="3"></label>
                <label class="field">
                    <span>Project type</span>
                    <select name="service_type">
                        <option value="">— any —</option>
                        <?php foreach ($types as $type): ?>
                            <option value="<?= e($type) ?>" <?= ($editing['service_type'] ?? '') === $type ? 'selected' : '' ?>><?= e($type) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>

            <span class="field__label">What's included</span>
            <div class="repeater" data-repeater>
                <?php $featureRows = ($editing['features'] ?? []) ?: ['']; ?>
                <?php foreach ($featureRows as $feature): ?>
                    <div class="repeater__row" data-repeater-row>
                        <input type="text" name="feature[]" value="<?= e((string) $feature) ?>" placeholder="e.g. 2 shoot days with director + DOP">
                        <button type="button" class="link link--danger" data-repeater-remove>Remove</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn--ghost btn--sm" data-repeater-add>Add feature</button>

            <label class="field"><span>Button label</span><input type="text" name="cta_label" value="<?= e((string) ($editing['cta_label'] ?? 'Enquire')) ?>"></label>

            <label class="switch-row">
                <span>Featured card</span>
                <span class="switch"><input type="checkbox" name="is_featured" <?= !empty($editing['is_featured']) ? 'checked' : '' ?>><i></i></span>
            </label>
            <label class="switch-row">
                <span>Show on the site</span>
                <span class="switch"><input type="checkbox" name="is_active" <?= !isset($editing) || $editing === null || !empty($editing['is_active']) ? 'checked' : '' ?>><i></i></span>
            </label>

            <div class="btn-row">
                <button class="btn btn--primary btn--sm" type="submit"><?= $editing ? 'Save Package' : 'Add Package' ?></button>
                <?php if ($editing): ?><a class="btn btn--ghost btn--sm" href="<?= url('/admin/packages') ?>">Cancel</a><?php endif; ?>
            </div>
        </form>
        <p class="muted">Section heading is editable in <a class="link" href="<?= url('/admin/content') ?>">Content → Service Packages</a>.</p>
    </article>
</div>
