<?php
/** @var array|null $project @var array $categories @var array $images @var array $videos @var array $gallery */
$isEdit = $project !== null;
$action = $isEdit ? url('/admin/projects/' . $project['id']) : url('/admin/projects');
$value = static fn (string $key, string $fallback = '') => e((string) ($project[$key] ?? $fallback));
?>
<form method="post" action="<?= $action ?>" class="cols cols--2-1 form">
    <?= csrf_field() ?>

    <div class="stack">
        <article class="card">
            <h2 class="card__label">PROJECT DETAILS</h2>
            <label class="field"><span>Title *</span><input type="text" name="title" value="<?= $value('title') ?>" required></label>
            <div class="field-row">
                <label class="field"><span>Client / Partner</span><input type="text" name="client_name" value="<?= $value('client_name') ?>"></label>
                <label class="field"><span>Year</span><input type="number" name="year" value="<?= $value('year', (string) date('Y')) ?>"></label>
            </div>
            <div class="field-row">
                <label class="field">
                    <span>Category</span>
                    <select name="category">
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= e($category) ?>" <?= ($project['category'] ?? '') === $category ? 'selected' : '' ?>><?= e($category) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="field"><span>Order</span><input type="number" name="sort_order" value="<?= $value('sort_order', '0') ?>"></label>
            </div>
            <label class="field"><span>Summary (card text)</span><input type="text" name="summary" value="<?= $value('summary') ?>" maxlength="255"></label>
            <label class="field"><span>Description</span><textarea name="description" rows="6"><?= $value('description') ?></textarea></label>
        </article>

        <article class="card">
            <h2 class="card__label">MEDIA</h2>
            <label class="field"><span>Hero video URL (YouTube / Vimeo)</span><input type="url" name="hero_video_url" value="<?= $value('hero_video_url') ?>" placeholder="https://vimeo.com/…"></label>
            <label class="field">
                <span>Linked video from the library</span>
                <select name="video_id">
                    <option value="">— none —</option>
                    <?php foreach ($videos as $video): ?>
                        <option value="<?= (int) $video['id'] ?>" <?= (int) ($project['video_id'] ?? 0) === (int) $video['id'] ? 'selected' : '' ?>>
                            <?= e($video['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <span class="field__label">Cover image</span>
            <div class="picker">
                <label class="picker__item <?= empty($project['cover_media_id']) ? 'is-selected' : '' ?>">
                    <input type="radio" name="cover_media_id" value="" <?= empty($project['cover_media_id']) ? 'checked' : '' ?>>
                    <span class="picker__none">None</span>
                </label>
                <?php foreach ($images as $image): ?>
                    <label class="picker__item <?= (int) ($project['cover_media_id'] ?? 0) === (int) $image['id'] ? 'is-selected' : '' ?>"
                           style="background-image:url(<?= e(uploaded($image['path'])) ?>)">
                        <input type="radio" name="cover_media_id" value="<?= (int) $image['id'] ?>" <?= (int) ($project['cover_media_id'] ?? 0) === (int) $image['id'] ? 'checked' : '' ?>>
                        <span class="picker__name"><?= e($image['filename']) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>

            <span class="field__label">Gallery stills</span>
            <div class="picker">
                <?php foreach ($images as $image): ?>
                    <label class="picker__item <?= in_array((int) $image['id'], array_map('intval', $gallery), true) ? 'is-selected' : '' ?>"
                           style="background-image:url(<?= e(uploaded($image['path'])) ?>)">
                        <input type="checkbox" name="gallery[]" value="<?= (int) $image['id'] ?>" <?= in_array((int) $image['id'], array_map('intval', $gallery), true) ? 'checked' : '' ?>>
                        <span class="picker__name"><?= e($image['filename']) ?></span>
                    </label>
                <?php endforeach; ?>
                <?php if (!$images): ?>
                    <p class="empty">No images yet — upload some in <a href="<?= url('/admin/media') ?>">Media</a>.</p>
                <?php endif; ?>
            </div>
        </article>
    </div>

    <div class="stack">
        <article class="card">
            <h2 class="card__label">PUBLISH</h2>
            <label class="field">
                <span>Status</span>
                <select name="status">
                    <?php foreach (['draft' => 'Draft', 'scheduled' => 'Scheduled', 'published' => 'Published'] as $key => $label): ?>
                        <option value="<?= $key ?>" <?= ($project['status'] ?? 'draft') === $key ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="switch-row">
                <span>Show on homepage</span>
                <span class="switch"><input type="checkbox" name="show_on_homepage" <?= !empty($project['show_on_homepage']) ? 'checked' : '' ?>><i></i></span>
            </label>
            <label class="switch-row">
                <span>Featured in reel</span>
                <span class="switch"><input type="checkbox" name="featured_in_reel" <?= !empty($project['featured_in_reel']) ? 'checked' : '' ?>><i></i></span>
            </label>
            <div class="btn-row">
                <button class="btn btn--primary btn--sm" type="submit"><?= $isEdit ? 'Save Changes' : 'Create Project' ?></button>
                <?php if ($isEdit): ?>
                    <a class="btn btn--ghost btn--sm" href="<?= url('/work/' . $project['slug']) ?>" target="_blank" rel="noopener">Preview</a>
                <?php endif; ?>
            </div>
        </article>

        <article class="card">
            <h2 class="card__label">SEO</h2>
            <label class="field"><span>Slug</span><input type="text" name="slug" value="<?= $value('slug') ?>" placeholder="auto from title"></label>
            <label class="field"><span>Meta title</span><input type="text" name="meta_title" value="<?= $value('meta_title') ?>"></label>
            <label class="field"><span>Meta description</span><textarea name="meta_description" rows="3"><?= $value('meta_description') ?></textarea></label>
        </article>

        <?php if ($isEdit): ?>
            <article class="card">
                <h2 class="card__label">DANGER ZONE</h2>
                <p class="muted">Deleting removes the project and its gallery links. Media files stay in the library.</p>
            </article>
        <?php endif; ?>
    </div>
</form>

<?php if ($isEdit): ?>
    <form method="post" action="<?= url('/admin/projects/' . $project['id'] . '/delete') ?>" data-confirm="Delete this project permanently?" class="delete-form">
        <?= csrf_field() ?>
        <button class="btn btn--danger btn--sm" type="submit">Delete project</button>
    </form>
<?php endif; ?>
