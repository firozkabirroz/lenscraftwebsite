<?php /** @var array $stats @var array $recentBookings @var array $recentVideos @var array $activity */ ?>
<div class="stat-row">
    <?php foreach ($stats as $stat): ?>
        <article class="card stat">
            <span class="stat__label"><?= e($stat['label']) ?></span>
            <strong class="stat__value"><?= e($stat['value']) ?></strong>
            <span class="stat__meta"><?= e($stat['meta']) ?></span>
        </article>
    <?php endforeach; ?>
</div>

<div class="cols cols--2-1">
    <article class="card">
        <header class="card__head">
            <h2>Recent bookings</h2>
            <a class="link-arrow" href="<?= url('/admin/bookings') ?>">All bookings →</a>
        </header>
        <table class="table">
            <thead>
            <tr><th>Code</th><th>Client</th><th>Type</th><th>Shoot date</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
            <?php foreach ($recentBookings as $booking): ?>
                <tr>
                    <td class="mono"><?= e($booking['code']) ?></td>
                    <td><strong><?= e($booking['client_name']) ?></strong></td>
                    <td><?= e($booking['project_type']) ?></td>
                    <td><?= e(date_pretty($booking['shoot_date'])) ?></td>
                    <td><span class="badge badge--<?= e(status_tone($booking['status'])) ?>"><?= e(ucfirst($booking['status'])) ?></span></td>
                    <td class="right"><a class="link" href="<?= url('/admin/bookings/' . $booking['id']) ?>">View</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$recentBookings): ?>
                <tr><td colspan="6" class="empty">No bookings yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </article>

    <article class="card">
        <header class="card__head">
            <h2>Unread messages</h2>
            <a class="link-arrow" href="<?= url('/admin/messages') ?>">Inbox →</a>
        </header>
        <ul class="list">
            <?php foreach ($unreadList as $message): ?>
                <li>
                    <a href="<?= url('/admin/messages') ?>?id=<?= (int) $message['id'] ?>">
                        <strong><?= e($message['name']) ?></strong>
                        <span><?= e(excerpt($message['subject'], 46)) ?></span>
                    </a>
                    <time><?= e(time_ago($message['created_at'])) ?></time>
                </li>
            <?php endforeach; ?>
            <?php if (!$unreadList): ?>
                <li class="empty">Inbox is clear.</li>
            <?php endif; ?>
        </ul>
    </article>
</div>

<div class="cols cols--1-1">
    <article class="card">
        <header class="card__head">
            <h2>Latest uploads</h2>
            <a class="link-arrow" href="<?= url('/admin/videos') ?>">Video library →</a>
        </header>
        <ul class="list">
            <?php foreach ($recentVideos as $video): ?>
                <li>
                    <a href="<?= url('/admin/videos/' . $video['id'] . '/edit') ?>">
                        <strong><?= e($video['title']) ?></strong>
                        <span><?= e($video['category']) ?> · <?= e($video['source'] === 'embed' ? ucfirst((string) $video['provider']) : human_size((int) $video['size_bytes'])) ?></span>
                    </a>
                    <span class="badge badge--<?= e(status_tone($video['status'])) ?>"><?= e(ucfirst($video['status'])) ?></span>
                </li>
            <?php endforeach; ?>
            <?php if (!$recentVideos): ?>
                <li class="empty">No videos yet — upload the showreel.</li>
            <?php endif; ?>
        </ul>
    </article>

    <article class="card">
        <header class="card__head">
            <h2>Activity</h2>
            <a class="link-arrow" href="<?= url('/admin/activity') ?>">Full log →</a>
        </header>
        <ul class="timeline">
            <?php foreach ($activity as $entry): ?>
                <li>
                    <span class="dot-mark"></span>
                    <div>
                        <strong><?= e($entry['user_name'] ?? 'System') ?></strong>
                        <?= e($entry['action']) ?>
                        <?php if ($entry['meta']): ?><em>“<?= e($entry['meta']) ?>”</em><?php endif; ?>
                        <time><?= e(time_ago($entry['created_at'])) ?></time>
                    </div>
                </li>
            <?php endforeach; ?>
            <?php if (!$activity): ?>
                <li class="empty">Nothing logged yet.</li>
            <?php endif; ?>
        </ul>
    </article>
</div>

<div class="cols cols--1-1-1">
    <article class="card mini">
        <span class="stat__label">SITE VISITS · 30 DAYS</span>
        <strong class="stat__value"><?= number_format($summary['visits']) ?></strong>
        <span class="stat__meta"><?= $summary['visits_change'] >= 0 ? '+' : '' ?><?= e((string) $summary['visits_change']) ?>% vs previous</span>
    </article>
    <article class="card mini">
        <span class="stat__label">CLIENTS</span>
        <strong class="stat__value"><?= number_format($clients) ?></strong>
        <span class="stat__meta">in the studio address book</span>
    </article>
    <article class="card mini">
        <span class="stat__label">MEDIA STORAGE</span>
        <strong class="stat__value"><?= e(human_size($storage['storage'])) ?></strong>
        <span class="stat__meta"><?= number_format($storage['count']) ?> files</span>
    </article>
</div>
