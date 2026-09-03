<?php /** @var array $booking @var array $items @var array $crew @var array $events @var array $statuses @var array $types */ ?>
<form method="post" action="<?= url('/admin/bookings/' . $booking['id']) ?>" class="cols cols--2-1 form">
    <?= csrf_field() ?>

    <div class="stack">
        <article class="card">
            <h2 class="card__label">CLIENT & REQUEST</h2>
            <div class="field-row">
                <label class="field"><span>Client name</span><input type="text" name="client_name" value="<?= e($booking['client_name']) ?>"></label>
                <label class="field"><span>Organisation</span><input type="text" name="organisation" value="<?= e((string) $booking['organisation']) ?>"></label>
            </div>
            <div class="field-row">
                <label class="field"><span>Email</span><input type="email" name="email" value="<?= e((string) $booking['email']) ?>"></label>
                <label class="field"><span>Phone</span><input type="text" name="phone" value="<?= e((string) $booking['phone']) ?>"></label>
            </div>
            <div class="field-row">
                <label class="field">
                    <span>Project type</span>
                    <select name="project_type">
                        <?php foreach ($types as $type): ?>
                            <option value="<?= e($type) ?>" <?= $booking['project_type'] === $type ? 'selected' : '' ?>><?= e($type) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="field"><span>Location</span><input type="text" name="location" value="<?= e((string) $booking['location']) ?>"></label>
            </div>
            <div class="field-row">
                <label class="field"><span>Shoot date</span><input type="date" name="shoot_date" value="<?= e((string) $booking['shoot_date']) ?>"></label>
                <label class="field"><span>Shoot days</span><input type="number" name="shoot_days" value="<?= (int) $booking['shoot_days'] ?>" min="1"></label>
            </div>
            <label class="field"><span>Budget (BDT)</span><input type="text" name="budget" value="<?= e((string) (int) $booking['budget']) ?>"></label>
            <?php if (!empty($package)): ?>
                <div class="notice">
                    <strong>Package:</strong> <?= e($package['name']) ?>
                    <span class="muted"> · <?= e(($package['price_label'] ?: 'From') . ' ' . money((float) $package['price_from'], (string) $package['currency'])) ?></span>
                </div>
            <?php endif; ?>
            <label class="field"><span>Project brief</span><textarea name="brief" rows="6"><?= e((string) $booking['brief']) ?></textarea></label>
        </article>

        <article class="card">
            <h2 class="card__label">CREW & SCHEDULE</h2>
            <div class="repeater" data-repeater>
                <?php $crewRows = $crew ?: [['person' => '', 'role' => '', 'days' => '']]; ?>
                <?php foreach ($crewRows as $member): ?>
                    <div class="repeater__row" data-repeater-row>
                        <input type="text" name="crew_person[]" value="<?= e((string) $member['person']) ?>" placeholder="Name">
                        <input type="text" name="crew_role[]" value="<?= e((string) $member['role']) ?>" placeholder="Role">
                        <input type="text" name="crew_days[]" value="<?= e((string) $member['days']) ?>" placeholder="Days">
                        <button type="button" class="link link--danger" data-repeater-remove>Remove</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn--ghost btn--sm" data-repeater-add>Add crew member</button>
        </article>

        <article class="card">
            <h2 class="card__label">QUOTE</h2>
            <div class="repeater" data-repeater>
                <?php $itemRows = $items ?: [['label' => 'Production', 'amount' => 0]]; ?>
                <?php foreach ($itemRows as $item): ?>
                    <div class="repeater__row repeater__row--2" data-repeater-row>
                        <input type="text" name="item_label[]" value="<?= e((string) $item['label']) ?>" placeholder="Line item">
                        <input type="text" name="item_amount[]" value="<?= e((string) (int) $item['amount']) ?>" placeholder="Amount">
                        <button type="button" class="link link--danger" data-repeater-remove>Remove</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn--ghost btn--sm" data-repeater-add>Add line item</button>
            <p class="quote-total">Current total <strong><?= e(money($booking['quote_total'])) ?></strong></p>
        </article>
    </div>

    <div class="stack">
        <article class="card">
            <h2 class="card__label">STATUS</h2>
            <label class="field">
                <span>Booking status</span>
                <select name="status">
                    <?php foreach ($statuses as $statusOption): ?>
                        <option value="<?= e($statusOption) ?>" <?= $booking['status'] === $statusOption ? 'selected' : '' ?>><?= e(ucfirst($statusOption)) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <ul class="kv">
                <li><span>Code</span><strong class="mono"><?= e($booking['code']) ?></strong></li>
                <li><span>Source</span><strong><?= e(ucfirst((string) $booking['source'])) ?></strong></li>
                <li><span>Created</span><strong><?= e(date_pretty($booking['created_at'], 'M d, Y')) ?></strong></li>
                <li><span>Updated</span><strong><?= e(time_ago($booking['updated_at'])) ?></strong></li>
            </ul>
            <div class="btn-row">
                <button class="btn btn--primary btn--sm" type="submit">Save Booking</button>
                <a class="btn btn--ghost btn--sm" href="<?= url('/admin/bookings') ?>">Back</a>
            </div>
        </article>

        <article class="card">
            <h2 class="card__label">TIMELINE</h2>
            <ul class="timeline">
                <?php foreach ($events as $event): ?>
                    <li>
                        <span class="dot-mark <?= $event['is_done'] ? '' : 'dot-mark--open' ?>"></span>
                        <div>
                            <strong><?= e($event['label']) ?></strong>
                            <?php if ($event['note']): ?><em><?= e($event['note']) ?></em><?php endif; ?>
                            <time><?= e($event['happened_at'] ? time_ago($event['happened_at']) : 'Pending') ?></time>
                        </div>
                    </li>
                <?php endforeach; ?>
                <?php if (!$events): ?><li class="empty">No events yet.</li><?php endif; ?>
            </ul>
        </article>

        <article class="card">
            <h2 class="card__label">INTERNAL NOTES</h2>
            <label class="field"><textarea name="internal_notes" rows="5" placeholder="Only the studio sees this."><?= e((string) $booking['internal_notes']) ?></textarea></label>
        </article>
    </div>
</form>

<div class="row-actions">
    <?php foreach (['confirmed' => 'Confirm Booking', 'completed' => 'Mark Completed', 'cancelled' => 'Cancel Booking'] as $statusKey => $label): ?>
        <form method="post" action="<?= url('/admin/bookings/' . $booking['id'] . '/status') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="status" value="<?= $statusKey ?>">
            <button class="btn <?= $statusKey === 'cancelled' ? 'btn--danger' : 'btn--primary' ?> btn--sm" type="submit"><?= $label ?></button>
        </form>
    <?php endforeach; ?>
</div>
