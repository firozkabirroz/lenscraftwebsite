<?php /** @var array $clients @var array|null $editing @var array $bookings */ ?>
<div class="cols cols--2-1">
    <article class="card card--flush">
        <table class="table table--wide">
            <thead>
            <tr><th>Client</th><th>Contact</th><th>Bookings</th><th>Booked value</th><th class="right">Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($clients as $client): ?>
                <tr>
                    <td>
                        <strong><?= e($client['name']) ?></strong>
                        <small><?= e($client['organisation'] ?: '—') ?></small>
                    </td>
                    <td>
                        <?= e($client['email'] ?: '—') ?><br>
                        <small><?= e($client['phone'] ?: '') ?></small>
                    </td>
                    <td><?= (int) $client['booking_count'] ?></td>
                    <td><?= e(money((float) $client['revenue'])) ?></td>
                    <td class="right actions">
                        <a class="link" href="<?= url('/admin/clients') ?>?edit=<?= (int) $client['id'] ?>#client-form">Edit</a>
                        <form method="post" action="<?= url('/admin/clients/' . $client['id'] . '/delete') ?>" data-confirm="Remove this client?">
                            <?= csrf_field() ?>
                            <button class="link link--danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$clients): ?>
                <tr><td colspan="5" class="empty">No clients yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </article>

    <article class="card" id="client-form">
        <h2 class="card__label"><?= $editing ? 'EDIT CLIENT' : 'ADD CLIENT' ?></h2>
        <form method="post" action="<?= $editing ? url('/admin/clients/' . $editing['id']) : url('/admin/clients') ?>">
            <?= csrf_field() ?>
            <label class="field"><span>Name *</span><input type="text" name="name" value="<?= e((string) ($editing['name'] ?? '')) ?>" required></label>
            <label class="field"><span>Organisation</span><input type="text" name="organisation" value="<?= e((string) ($editing['organisation'] ?? '')) ?>"></label>
            <label class="field"><span>Email</span><input type="email" name="email" value="<?= e((string) ($editing['email'] ?? '')) ?>"></label>
            <label class="field"><span>Phone</span><input type="text" name="phone" value="<?= e((string) ($editing['phone'] ?? '')) ?>"></label>
            <label class="field"><span>Notes</span><textarea name="notes" rows="4"><?= e((string) ($editing['notes'] ?? '')) ?></textarea></label>
            <div class="btn-row">
                <button class="btn btn--primary btn--sm" type="submit"><?= $editing ? 'Save Client' : 'Add Client' ?></button>
                <?php if ($editing): ?><a class="btn btn--ghost btn--sm" href="<?= url('/admin/clients') ?>">Cancel</a><?php endif; ?>
            </div>
        </form>
    </article>
</div>
