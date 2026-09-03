<?php /** @var array $types @var array $clients @var string $nextCode */ ?>
<form method="post" action="<?= url('/admin/bookings') ?>" class="cols cols--2-1 form">
    <?= csrf_field() ?>

    <div class="stack">
        <article class="card">
            <h2 class="card__label">CLIENT</h2>
            <div class="field-row">
                <label class="field"><span>Client name *</span><input type="text" name="client_name" list="clientNames" required></label>
                <label class="field"><span>Organisation</span><input type="text" name="organisation"></label>
            </div>
            <datalist id="clientNames">
                <?php foreach ($clients as $client): ?>
                    <option value="<?= e($client['name']) ?>"></option>
                <?php endforeach; ?>
            </datalist>
            <div class="field-row">
                <label class="field"><span>Email</span><input type="email" name="email"></label>
                <label class="field"><span>Phone</span><input type="text" name="phone" placeholder="+880"></label>
            </div>
        </article>

        <article class="card">
            <h2 class="card__label">SHOOT DETAILS</h2>
            <div class="field-row">
                <label class="field">
                    <span>Project type</span>
                    <select name="project_type">
                        <?php foreach ($types as $type): ?>
                            <option value="<?= e($type) ?>"><?= e($type) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="field"><span>Location</span><input type="text" name="location" placeholder="City / venue"></label>
            </div>
            <div class="field-row">
                <label class="field"><span>Shoot date</span><input type="date" name="shoot_date"></label>
                <label class="field"><span>Shoot days</span><input type="number" name="shoot_days" value="1" min="1"></label>
            </div>
            <label class="field"><span>Budget (BDT)</span><input type="text" name="budget" placeholder="400000"></label>
            <label class="field"><span>Brief</span><textarea name="brief" rows="6" placeholder="Format, deliverables, deadline…"></textarea></label>
        </article>
    </div>

    <div class="stack">
        <article class="card">
            <h2 class="card__label">SUMMARY</h2>
            <ul class="kv">
                <li><span>Booking code</span><strong class="mono"><?= e($nextCode) ?></strong></li>
                <li><span>Created by</span><strong><?= e($user['name'] ?? 'Admin') ?></strong></li>
                <li><span>Source</span><strong>Admin panel</strong></li>
            </ul>
            <label class="field">
                <span>Status</span>
                <select name="status">
                    <option value="pending" selected>Pending</option>
                    <option value="inquiry">Inquiry</option>
                    <option value="confirmed">Confirmed</option>
                </select>
            </label>
            <div class="btn-row">
                <button class="btn btn--primary btn--sm" type="submit">Save Booking</button>
                <a class="btn btn--ghost btn--sm" href="<?= url('/admin/bookings') ?>">Cancel</a>
            </div>
        </article>
    </div>
</form>
