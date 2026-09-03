<?php
/** @var array $categories @var array $projects @var int $chunkSize @var int $maxVideo */
$pageScript = 'upload.js';
?>
<form method="post" action="<?= url('/admin/videos') ?>" class="cols cols--2-1 form" id="videoForm">
    <?= csrf_field() ?>
    <input type="hidden" name="source" id="sourceInput" value="local">
    <input type="hidden" name="file_path" id="filePathInput" value="">
    <input type="hidden" name="size_bytes" id="sizeInput" value="0">
    <input type="hidden" name="duration_seconds" id="durationInput" value="0">

    <div class="stack">
        <article class="card">
            <div class="tabs" id="sourceTabs">
                <button type="button" class="tab is-active" data-source="local">Upload a file</button>
                <button type="button" class="tab" data-source="embed">YouTube / Vimeo link</button>
            </div>

            <div class="pane" data-pane="local">
                <div class="dropzone" id="dropzone"
                     data-chunk-size="<?= $chunkSize ?>"
                     data-max-size="<?= $maxVideo ?>"
                     data-endpoint="<?= url('/admin/videos/chunk') ?>">
                    <strong>Drag & drop a video here</strong>
                    <span>MP4, MOV, MKV or WebM · up to <?= e(human_size($maxVideo)) ?> · uploaded in <?= e(human_size($chunkSize)) ?> chunks</span>
                    <label class="btn btn--ghost btn--sm">
                        Choose file
                        <input type="file" id="videoFile" accept="video/*" hidden>
                    </label>
                </div>

                <div class="upload-list" id="uploadList"></div>
            </div>

            <div class="pane" data-pane="embed" hidden>
                <label class="field">
                    <span>Video URL</span>
                    <input type="url" name="embed_url" id="embedUrl" placeholder="https://vimeo.com/22439234">
                </label>
                <p class="muted">Paste a YouTube or Vimeo link — the site embeds the player and no file is stored on the server.</p>
            </div>
        </article>

        <article class="card">
            <h2 class="card__label">VIDEO DETAILS</h2>
            <label class="field"><span>Title *</span><input type="text" name="title" id="titleInput" required></label>
            <div class="field-row">
                <label class="field">
                    <span>Category</span>
                    <select name="category">
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= e($category) ?>"><?= e($category) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="field">
                    <span>Attach to project</span>
                    <select name="project_id">
                        <option value="">— none —</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?= (int) $project['id'] ?>"><?= e($project['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>
        </article>
    </div>

    <div class="stack">
        <article class="card">
            <h2 class="card__label">PLACEMENT</h2>
            <label class="switch-row"><span>Publish immediately</span><span class="switch"><input type="checkbox" name="is_published" checked><i></i></span></label>
            <label class="switch-row"><span>Home hero reel</span><span class="switch"><input type="checkbox" name="place_home_hero"><i></i></span></label>
            <label class="switch-row"><span>Work grid</span><span class="switch"><input type="checkbox" name="place_work_grid" checked><i></i></span></label>
            <label class="switch-row"><span>Services page</span><span class="switch"><input type="checkbox" name="place_services"><i></i></span></label>
            <div class="btn-row">
                <button class="btn btn--primary btn--sm" type="submit" id="saveVideo">Save Video</button>
                <a class="btn btn--ghost btn--sm" href="<?= url('/admin/videos') ?>">Cancel</a>
            </div>
        </article>

        <article class="card">
            <h2 class="card__label">UPLOAD LIMITS</h2>
            <ul class="kv">
                <li><span>Chunk size</span><strong><?= e(human_size($chunkSize)) ?></strong></li>
                <li><span>Max video</span><strong><?= e(human_size($maxVideo)) ?></strong></li>
                <li><span>PHP upload_max_filesize</span><strong><?= e(ini_get('upload_max_filesize')) ?></strong></li>
                <li><span>PHP post_max_size</span><strong><?= e(ini_get('post_max_size')) ?></strong></li>
            </ul>
            <p class="muted">Chunked upload keeps each request small, so large files work even with a modest PHP limit.</p>
        </article>
    </div>
</form>
