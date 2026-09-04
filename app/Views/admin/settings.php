<?php /** @var array $values @var array $users @var bool $isOwner */ ?>
<div class="cols cols--2-1">
    <div class="stack">
        <article class="card">
            <h2 class="card__label">STUDIO PROFILE</h2>
            <form method="post" action="<?= url('/admin/settings') ?>">
                <?= csrf_field() ?>
                <div class="field-row">
                    <label class="field"><span>Studio name</span><input type="text" name="studio_name" value="<?= e($values['studio_name'] ?? '') ?>"></label>
                    <label class="field"><span>Tagline</span><input type="text" name="tagline" value="<?= e($values['tagline'] ?? '') ?>"></label>
                </div>
                <div class="field-row">
                    <label class="field"><span>Email</span><input type="email" name="email" value="<?= e($values['email'] ?? '') ?>"></label>
                    <label class="field"><span>Phone</span><input type="text" name="phone" value="<?= e($values['phone'] ?? '') ?>"></label>
                </div>
                <div class="field-row">
                    <label class="field"><span>WhatsApp</span><input type="text" name="whatsapp" value="<?= e($values['whatsapp'] ?? '') ?>"></label>
                    <label class="field"><span>Hours</span><input type="text" name="hours" value="<?= e($values['hours'] ?? '') ?>"></label>
                </div>
                <label class="field"><span>Address</span><input type="text" name="address" value="<?= e($values['address'] ?? '') ?>"></label>
                <div class="field-row">
                    <label class="field"><span>Instagram</span><input type="url" name="instagram" value="<?= e($values['instagram'] ?? '') ?>"></label>
                    <label class="field"><span>YouTube</span><input type="url" name="youtube" value="<?= e($values['youtube'] ?? '') ?>"></label>
                </div>
                <label class="field"><span>Facebook</span><input type="url" name="facebook" value="<?= e($values['facebook'] ?? '') ?>"></label>
                <label class="field"><span>Meta description</span><textarea name="meta_description" rows="3"><?= e($values['meta_description'] ?? '') ?></textarea></label>
                <label class="field"><span>Footer note</span><input type="text" name="footer_note" value="<?= e($values['footer_note'] ?? '') ?>"></label>

                <h3 class="card__label">NOTIFICATIONS</h3>
                <label class="switch-row"><span>Email me on a new booking</span><span class="switch"><input type="checkbox" name="notify_booking" <?= ($values['notify_booking'] ?? '0') === '1' ? 'checked' : '' ?>><i></i></span></label>
                <label class="switch-row"><span>Email me on a new message</span><span class="switch"><input type="checkbox" name="notify_message" <?= ($values['notify_message'] ?? '0') === '1' ? 'checked' : '' ?>><i></i></span></label>
                <label class="switch-row"><span>Notify when an upload finishes</span><span class="switch"><input type="checkbox" name="notify_upload" <?= ($values['notify_upload'] ?? '0') === '1' ? 'checked' : '' ?>><i></i></span></label>

                <div class="btn-row"><button class="btn btn--primary btn--sm" type="submit">Save Settings</button></div>
            </form>
        </article>

        <article class="card">
            <h2 class="card__label">ADMIN USERS</h2>
            <table class="table">
                <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Last login</th></tr></thead>
                <tbody>
                <?php foreach ($users as $account): ?>
                    <tr>
                        <td><strong><?= e($account['name']) ?></strong></td>
                        <td><?= e($account['email']) ?></td>
                        <td><span class="badge badge--<?= $account['role'] === 'owner' ? 'accent' : 'muted' ?>"><?= e(ucfirst($account['role'])) ?></span></td>
                        <td><?= e($account['last_login_at'] ? time_ago($account['last_login_at']) : 'never') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($isOwner): ?>
                <h3 class="card__label">ADD A USER</h3>
                <form method="post" action="<?= url('/admin/settings/users') ?>">
                    <?= csrf_field() ?>
                    <div class="field-row">
                        <label class="field"><span>Name</span><input type="text" name="name" required></label>
                        <label class="field"><span>Email</span><input type="email" name="email" required></label>
                    </div>
                    <div class="field-row">
                        <label class="field"><span>Password (8+ characters)</span><input type="password" name="password" required></label>
                        <label class="field">
                            <span>Role</span>
                            <select name="role"><option value="editor">Editor</option><option value="owner">Owner</option></select>
                        </label>
                    </div>
                    <div class="btn-row"><button class="btn btn--primary btn--sm" type="submit">Create User</button></div>
                </form>
            <?php else: ?>
                <p class="muted">Only the studio owner can add admin users.</p>
            <?php endif; ?>
        </article>
    </div>

    <div class="stack">
        <article class="card">
            <h2 class="card__label">YOUR PASSWORD</h2>
            <form method="post" action="<?= url('/admin/settings/password') ?>">
                <?= csrf_field() ?>
                <label class="field"><span>Current password</span><input type="password" name="current_password" required autocomplete="current-password"></label>
                <label class="field"><span>New password</span><input type="password" name="new_password" required minlength="8" autocomplete="new-password"></label>
                <label class="field"><span>Confirm new password</span><input type="password" name="new_password_confirm" required minlength="8" autocomplete="new-password"></label>
                <p class="muted">New password is saved as a secure hash in the database — never in the code.</p>
                <div class="btn-row"><button class="btn btn--primary btn--sm" type="submit">Update Password</button></div>
            </form>
        </article>

        <article class="card">
            <h2 class="card__label">SYSTEM</h2>
            <ul class="kv">
                <li><span>PHP version</span><strong><?= e(PHP_VERSION) ?></strong></li>
                <li><span>Upload limit</span><strong><?= e(ini_get('upload_max_filesize')) ?></strong></li>
                <li><span>Environment</span><strong><?= e((string) config('app_env')) ?></strong></li>
                <li><span>Timezone</span><strong><?= e(date_default_timezone_get()) ?></strong></li>
            </ul>
            <a class="link-arrow" href="<?= url('/admin/activity') ?>">View full activity log →</a>
        </article>
    </div>
</div>
