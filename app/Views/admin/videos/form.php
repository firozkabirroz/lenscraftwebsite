<?php
/** @var array $video @var array $categories @var array $projects @var array $images */
$src = $video['source'] === 'embed' ? embed_url($video['embed_url']) : uploaded($video['file_path']);
?>
<form method="post" action="<?= url('/admin/videos/' . $video['id']) ?>" class="cols cols--2-1 form">
    <?= csrf_field() ?>

    <div class="stack">
        <article class="card">
            <div class="player">
                <?php if ($video['source'] === 'embed'): ?>
                    <iframe src="<?= e($src) ?>" title="<?= e($video['title']) ?>" allowfullscreen loading="lazy"></iframe>
                <?php elseif ($video['file_path']): ?>
                    <video src="<?= e($src) ?>" controls preload="metadata"
                           <?= $video['poster_path'] ? 'poster="' . e(uploaded($video['poster_path'])) . '"' : '' ?>></video>
                <?php else: ?>
                    <div class="player__empty">No file attached</div>
                <?php endif; ?>
            </div>
            <ul class="kv kv--row">
                <li><span>Source</span><strong><?= e($video['source'] === 'embed' ? ucfirst((string) $video['provider']) : 'Local file') ?></strong></li>
                <li><span>Size</span><strong><?= e($video['size_bytes'] ? human_size((int) $video['size_bytes']) : '—') ?></strong></li>
                <li><span>Duration</span><strong><?= e(duration_format((int) $video['duration_seconds'])) ?></strong></li>
                <li><span>Views</span><strong><?= number_format((int) $video['views']) ?></strong></li>
            </ul>
        </article>

        <article class="card">
            <h2 class="card__label">VIDEO DETAILS</h2>
            <label class="field"><span>Title</span><input type="text" name="title" value="<?= e($video['title']) ?>" required></label>
            <div class="field-row">
                <label class="field">
                    <span>Category</span>
                    <select name="category">
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= e($category) ?>" <?= $video['category'] === $category ? 'selected' : '' ?>><?= e($category) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="field">
                    <span>Attached project</span>
                    <select name="project_id">
                        <option value="">— none —</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?= (int) $project['id'] ?>" <?= (int) $video['project_id'] === (int) $project['id'] ? 'selected' : '' ?>>
                                <?= e($project['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>
            <div class="field-row">
                <label class="field">
                    <span>Source</span>
                    <select name="source">
                        <option value="local" <?= $video['source'] === 'local' ? 'selected' : '' ?>>Local file</option>
                        <option value="embed" <?= $video['source'] === 'embed' ? 'selected' : '' ?>>Embed link</option>
                    </select>
                </label>
                <label class="field"><span>Duration (seconds)</span><input type="number" name="duration_seconds" value="<?= (int) $video['duration_seconds'] ?>"></label>
            </div>
            <label class="field"><span>Embed URL</span><input type="url" name="embed_url" value="<?= e((string) $video['embed_url']) ?>" placeholder="https://vimeo.com/…"></label>

            <span class="field__label">Poster image</span>
            <div class="picker">
                <label class="picker__item <?= empty($video['poster_media_id']) ? 'is-selected' : '' ?>">
                    <input type="radio" name="poster_media_id" value="" <?= empty($video['poster_media_id']) ? 'checked' : '' ?>>
                    <span class="picker__none">None</span>
                </label>
                <?php foreach ($images as $image): ?>
                    <label class="picker__item <?= (int) $video['poster_media_id'] === (int) $image['id'] ? 'is-selected' : '' ?>"
                           style="background-image:url(<?= e(uploaded($image['path'])) ?>)">
                        <input type="radio" name="poster_media_id" value="<?= (int) $image['id'] ?>" <?= (int) $video['poster_media_id'] === (int) $image['id'] ? 'checked' : '' ?>>
                        <span class="picker__name"><?= e($image['filename']) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </article>
    </div>

    <div class="stack">
        <article class="card">
            <h2 class="card__label">STATUS</h2>
            <label class="field">
                <span>Encoding status</span>
                <select name="status">
                    <?php foreach (['ready' => 'Ready', 'processing' => 'Processing', 'failed' => 'Failed'] as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $video['status'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="switch-row"><span>Published</span><span class="switch"><input type="checkbox" name="is_published" <?= $video['is_published'] ? 'checked' : '' ?>><i></i></span></label>
            <label class="switch-row"><span>Home hero reel</span><span class="switch"><input type="checkbox" name="place_home_hero" <?= $video['place_home_hero'] ? 'checked' : '' ?>><i></i></span></label>
            <label class="switch-row"><span>Work grid</span><span class="switch"><input type="checkbox" name="place_work_grid" <?= $video['place_work_grid'] ? 'checked' : '' ?>><i></i></span></label>
            <label class="switch-row"><span>Services page</span><span class="switch"><input type="checkbox" name="place_services" <?= $video['place_services'] ? 'checked' : '' ?>><i></i></span></label>
            <div class="btn-row">
                <button class="btn btn--primary btn--sm" type="submit">Save Video</button>
                <a class="btn btn--ghost btn--sm" href="<?= url('/admin/videos') ?>">Back</a>
            </div>
        </article>

        <article class="card">
            <h2 class="card__label">FILE</h2>
            <ul class="kv">
                <li><span>Path</span><strong class="mono"><?= e($video['file_path'] ?: '—') ?></strong></li>
                <li><span>Uploaded</span><strong><?= e(date_pretty($video['created_at'], 'M d, Y H:i')) ?></strong></li>
                <li><span>Updated</span><strong><?= e(time_ago($video['updated_at'])) ?></strong></li>
            </ul>
        </article>
    </div>
</form>

<form method="post" action="<?= url('/admin/videos/' . $video['id'] . '/delete') ?>" data-confirm="Delete this video and its file?" class="delete-form">
    <?= csrf_field() ?>
    <button class="btn btn--danger btn--sm" type="submit">Delete video</button>
</form>
