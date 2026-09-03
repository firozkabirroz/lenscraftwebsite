<?php /** @var array $messages @var array|null $selected @var array $replies @var int $unread */ ?>
<div class="inbox">
    <aside class="inbox__list card card--flush">
        <header class="inbox__head">
            <strong>Inbox</strong>
            <span class="badge badge--accent"><?= (int) $unread ?> unread</span>
        </header>
        <div class="chips chips--pad">
            <?php foreach (['' => 'All', 'unread' => 'Unread', 'read' => 'Read', 'archived' => 'Archived'] as $key => $label): ?>
                <a class="chip <?= $status === $key ? 'is-active' : '' ?>" href="<?= url('/admin/messages') ?><?= $key ? '?status=' . $key : '' ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
        </div>
        <ul>
            <?php foreach ($messages as $message): ?>
                <li class="<?= $selected && (int) $selected['id'] === (int) $message['id'] ? 'is-active' : '' ?> <?= $message['status'] === 'unread' ? 'is-unread' : '' ?>">
                    <a href="<?= url('/admin/messages') ?>?id=<?= (int) $message['id'] ?><?= $status ? '&status=' . e($status) : '' ?>">
                        <div class="inbox__row">
                            <strong><?= e($message['name']) ?></strong>
                            <time><?= e(time_ago($message['created_at'])) ?></time>
                        </div>
                        <span class="inbox__subject"><?= e($message['subject']) ?></span>
                        <span class="inbox__preview"><?= e(excerpt($message['body'], 70)) ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
            <?php if (!$messages): ?><li class="empty">No messages here.</li><?php endif; ?>
        </ul>
    </aside>

    <section class="inbox__read card">
        <?php if ($selected): ?>
            <header class="inbox__readhead">
                <div>
                    <h2><?= e($selected['subject']) ?></h2>
                    <p><?= e($selected['name']) ?> · <a href="mailto:<?= e((string) $selected['email']) ?>"><?= e((string) $selected['email']) ?></a> · <?= e((string) $selected['phone']) ?></p>
                </div>
                <span class="badge badge--<?= e(status_tone($selected['status'])) ?>"><?= e(ucfirst($selected['status'])) ?></span>
            </header>

            <div class="message-body"><?= nl2br(e($selected['body'])) ?></div>

            <?php if ($replies): ?>
                <h3 class="card__label">REPLIES</h3>
                <ul class="reply-list">
                    <?php foreach ($replies as $reply): ?>
                        <li>
                            <strong><?= e($reply['user_name'] ?? 'Studio') ?></strong>
                            <time><?= e(time_ago($reply['created_at'])) ?></time>
                            <p><?= nl2br(e($reply['body'])) ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form method="post" action="<?= url('/admin/messages/' . $selected['id'] . '/reply') ?>" class="reply-form">
                <?= csrf_field() ?>
                <label class="field">
                    <span>Reply</span>
                    <textarea name="body" rows="5" placeholder="Write a reply…"></textarea>
                </label>
                <button class="btn btn--primary btn--sm" type="submit">Send Reply</button>
            </form>

            <div class="btn-row">
                <form method="post" action="<?= url('/admin/messages/' . $selected['id'] . '/booking') ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn--ghost btn--sm" type="submit">
                        <?= $selected['booking_id'] ? 'Open Booking' : 'Create Booking' ?>
                    </button>
                </form>

                <form method="post" action="<?= url('/admin/messages/' . $selected['id'] . '/status') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="status" value="archived">
                    <button class="btn btn--ghost btn--sm" type="submit">Archive</button>
                </form>

                <form method="post" action="<?= url('/admin/messages/' . $selected['id'] . '/delete') ?>" data-confirm="Delete this message?">
                    <?= csrf_field() ?>
                    <button class="btn btn--danger btn--sm" type="submit">Delete</button>
                </form>
            </div>
        <?php else: ?>
            <p class="empty">Select a message from the list.</p>
        <?php endif; ?>
    </section>
</div>
