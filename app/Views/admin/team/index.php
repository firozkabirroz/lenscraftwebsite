<?php /** @var array $members @var array|null $editing @var array $images */ ?>
<div class="cols cols--2-1">
    <article class="card card--flush">
        <table class="table table--wide">
            <thead>
            <tr><th>Order</th><th>Member</th><th>Role</th><th>Status</th><th class="right">Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($members as $member): ?>
                <tr>
                    <td>
                        <div class="order-controls">
                            <form method="post" action="<?= url('/admin/team/' . $member['id'] . '/move') ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="direction" value="up">
                                <button class="link" type="submit" title="Move up">↑</button>
                            </form>
                            <span class="mono"><?= str_pad((string) $member['sort_order'], 2, '0', STR_PAD_LEFT) ?></span>
                            <form method="post" action="<?= url('/admin/team/' . $member['id'] . '/move') ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="direction" value="down">
                                <button class="link" type="submit" title="Move down">↓</button>
                            </form>
                        </div>
                    </td>
                    <td>
                        <div class="cell-media">
                            <span class="thumb" <?= $member['photo_path'] ? 'style="background-image:url(' . e(uploaded($member['photo_path'])) . ')"' : '' ?>></span>
                            <strong><?= e($member['name']) ?></strong>
                        </div>
                    </td>
                    <td><?= e($member['role'] ?: '—') ?></td>
                    <td><span class="badge badge--<?= $member['is_active'] ? 'success' : 'muted' ?>"><?= $member['is_active'] ? 'Visible' : 'Hidden' ?></span></td>
                    <td class="right actions">
                        <a class="link" href="<?= url('/admin/team') ?>?edit=<?= (int) $member['id'] ?>#team-form">Edit</a>
                        <form method="post" action="<?= url('/admin/team/' . $member['id'] . '/delete') ?>" data-confirm="Remove this team member?">
                            <?= csrf_field() ?>
                            <button class="link link--danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$members): ?>
                <tr><td colspan="5" class="empty">No team members yet — add the first one on the right.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </article>

    <article class="card" id="team-form">
        <h2 class="card__label"><?= $editing ? 'EDIT MEMBER' : 'ADD MEMBER' ?></h2>
        <form method="post" action="<?= $editing ? url('/admin/team/' . $editing['id']) : url('/admin/team') ?>">
            <?= csrf_field() ?>
            <label class="field"><span>Name *</span><input type="text" name="name" value="<?= e((string) ($editing['name'] ?? '')) ?>" required></label>
            <label class="field"><span>Role / title</span><input type="text" name="role" value="<?= e((string) ($editing['role'] ?? '')) ?>" placeholder="Director / DOP"></label>
            <label class="field"><span>Short bio</span><textarea name="bio" rows="3"><?= e((string) ($editing['bio'] ?? '')) ?></textarea></label>
            <label class="field"><span>Order</span><input type="number" name="sort_order" value="<?= e((string) ($editing['sort_order'] ?? '')) ?>"></label>

            <span class="field__label">Photo (upload in <a class="link" href="<?= url('/admin/media') ?>">Media</a> first)</span>
            <div class="picker">
                <label class="picker__item <?= empty($editing['photo_media_id']) ? 'is-selected' : '' ?>">
                    <input type="radio" name="photo_media_id" value="" <?= empty($editing['photo_media_id']) ? 'checked' : '' ?>>
                    <span class="picker__none">None</span>
                </label>
                <?php foreach ($images as $image): ?>
                    <label class="picker__item <?= (int) ($editing['photo_media_id'] ?? 0) === (int) $image['id'] ? 'is-selected' : '' ?>"
                           style="background-image:url(<?= e(uploaded($image['path'])) ?>)">
                        <input type="radio" name="photo_media_id" value="<?= (int) $image['id'] ?>" <?= (int) ($editing['photo_media_id'] ?? 0) === (int) $image['id'] ? 'checked' : '' ?>>
                        <span class="picker__name"><?= e($image['filename']) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>

            <label class="switch-row">
                <span>Show on the site</span>
                <span class="switch"><input type="checkbox" name="is_active" <?= !isset($editing) || $editing === null || !empty($editing['is_active']) ? 'checked' : '' ?>><i></i></span>
            </label>

            <div class="btn-row">
                <button class="btn btn--primary btn--sm" type="submit"><?= $editing ? 'Save Member' : 'Add Member' ?></button>
                <?php if ($editing): ?><a class="btn btn--ghost btn--sm" href="<?= url('/admin/team') ?>">Cancel</a><?php endif; ?>
            </div>
        </form>
    </article>
</div>
