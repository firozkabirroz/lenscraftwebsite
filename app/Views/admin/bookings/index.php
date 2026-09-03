<?php /** @var array $bookings @var array $stats @var string $status */ ?>
<div class="stat-row">
    <article class="card stat"><span class="stat__label">OPEN</span><strong class="stat__value"><?= number_format($stats['open']) ?></strong><span class="stat__meta">inquiry + pending</span></article>
    <article class="card stat"><span class="stat__label">CONFIRMED</span><strong class="stat__value"><?= number_format($stats['by_status']['confirmed']) ?></strong><span class="stat__meta">shoot dates locked</span></article>
    <article class="card stat"><span class="stat__label">COMPLETED</span><strong class="stat__value"><?= number_format($stats['by_status']['completed']) ?></strong><span class="stat__meta">delivered projects</span></article>
    <article class="card stat"><span class="stat__label">BOOKED VALUE</span><strong class="stat__value"><?= e(money($stats['revenue'])) ?></strong><span class="stat__meta">confirmed + completed</span></article>
</div>

<div class="toolbar">
    <div class="chips">
        <?php foreach (['' => 'All', 'inquiry' => 'Inquiry', 'pending' => 'Pending', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $key => $label): ?>
            <a class="chip <?= $status === $key ? 'is-active' : '' ?>" href="<?= url('/admin/bookings') ?><?= $key ? '?status=' . $key : '' ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
    </div>
    <a class="btn btn--primary btn--sm" href="<?= url('/admin/bookings/create') ?>">Add Booking</a>
</div>

<article class="card card--flush">
    <table class="table table--wide">
        <thead>
        <tr><th>Code</th><th>Client</th><th>Type</th><th>Shoot date</th><th>Days</th><th>Quote</th><th>Status</th><th class="right">Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($bookings as $booking): ?>
            <tr>
                <td class="mono"><?= e($booking['code']) ?></td>
                <td>
                    <strong><?= e($booking['client_name']) ?></strong>
                    <small><?= e($booking['organisation'] ?: $booking['email']) ?></small>
                </td>
                <td><?= e($booking['project_type']) ?></td>
                <td><?= e(date_pretty($booking['shoot_date'])) ?></td>
                <td><?= (int) $booking['shoot_days'] ?></td>
                <td><?= e($booking['quote_total'] > 0 ? money($booking['quote_total']) : '—') ?></td>
                <td><span class="badge badge--<?= e(status_tone($booking['status'])) ?>"><?= e(ucfirst($booking['status'])) ?></span></td>
                <td class="right actions">
                    <a class="link" href="<?= url('/admin/bookings/' . $booking['id']) ?>">View</a>
                    <form method="post" action="<?= url('/admin/bookings/' . $booking['id'] . '/delete') ?>" data-confirm="Delete booking <?= e($booking['code']) ?>?">
                        <?= csrf_field() ?>
                        <button class="link link--danger" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$bookings): ?>
            <tr><td colspan="8" class="empty">No bookings in this view.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</article>
