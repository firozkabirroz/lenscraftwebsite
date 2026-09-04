<?php /** @var array $info @var array $types @var array $packages @var array|null $selectedPackage */ ?>
<section class="page-hero">
    <p class="eyebrow">Contact</p>
    <h1><?= e($info['heading'] ?? 'Start a project') ?></h1>
    <p class="page-hero__sub"><?= e($info['subheading'] ?? '') ?></p>
</section>

<section class="section contact">
    <aside class="contact__info">
        <div class="info-block">
            <span>Phone</span>
            <a href="tel:<?= e(str_replace(' ', '', $info['phone'] ?? '')) ?>"><?= e($info['phone'] ?? '') ?></a>
        </div>
        <div class="info-block">
            <span>Email</span>
            <a href="mailto:<?= e($info['email'] ?? '') ?>"><?= e($info['email'] ?? '') ?></a>
        </div>
        <div class="info-block">
            <span>Studio</span>
            <p><?= e($info['address'] ?? '') ?></p>
        </div>
        <div class="info-block">
            <span>Hours</span>
            <p><?= e($info['hours'] ?? '') ?></p>
        </div>
    </aside>

    <form class="contact__form" method="post" action="<?= url('/contact') ?>">
        <?= csrf_field() ?>
        <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
        <input type="hidden" name="package_id" value="<?= (int) ($selectedPackage['id'] ?? old('package_id', 0)) ?>">
        <input type="hidden" name="package_slug" value="<?= e((string) ($selectedPackage['slug'] ?? old('package_slug', ''))) ?>">

        <?php foreach (flash_all() as $message): ?>
            <div class="flash flash--<?= e($message['type']) ?>"><?= e($message['message']) ?></div>
        <?php endforeach; ?>

        <?php if ($selectedPackage): ?>
            <div class="package-pick package-pick--selected">
                <span class="package-pick__label">Selected package</span>
                <strong><?= e($selectedPackage['name']) ?></strong>
                <?php if (!empty($selectedPackage['tagline'])): ?>
                    <span class="package-pick__price"><?= e($selectedPackage['tagline']) ?></span>
                <?php endif; ?>
                <a class="link" href="<?= url('/contact') ?>">Change package</a>
            </div>
        <?php elseif ($packages): ?>
            <label class="field">
                <span>Interested in a package?</span>
                <select name="package_id" onchange="this.form.package_slug.value=''">
                    <option value="">Custom brief — no package selected</option>
                    <?php $picked = (int) old('package_id', 0); ?>
                    <?php foreach ($packages as $package): ?>
                        <option value="<?= (int) $package['id'] ?>" <?= $picked === (int) $package['id'] ? 'selected' : '' ?>>
                            <?= e($package['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        <?php endif; ?>

        <div class="field-row">
            <label class="field">
                <span>Your name *</span>
                <input type="text" name="name" value="<?= e((string) old('name')) ?>" required>
            </label>
            <label class="field">
                <span>Organisation</span>
                <input type="text" name="organisation" value="<?= e((string) old('organisation')) ?>">
            </label>
        </div>

        <div class="field-row">
            <label class="field">
                <span>Email *</span>
                <input type="email" name="email" value="<?= e((string) old('email')) ?>" required>
            </label>
            <label class="field">
                <span>Phone</span>
                <input type="text" name="phone" value="<?= e((string) old('phone')) ?>" placeholder="+880">
            </label>
        </div>

        <label class="field">
            <span>Project type</span>
            <select name="project_type">
                <?php
                $preset = (string) ($selectedPackage['service_type'] ?? request('type', old('project_type')));
                ?>
                <?php foreach ($types as $type): ?>
                    <option value="<?= e($type) ?>" <?= $preset === $type ? 'selected' : '' ?>><?= e($type) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <div class="field-row">
            <label class="field">
                <span>Preferred shoot date</span>
                <input type="date" name="shoot_date" value="<?= e((string) old('shoot_date')) ?>">
            </label>
            <label class="field">
                <span>Location</span>
                <input type="text" name="location" value="<?= e((string) old('location')) ?>" placeholder="City / venue">
            </label>
        </div>

        <label class="field">
            <span>Budget range (BDT)</span>
            <input type="text" name="budget" value="<?= e((string) old('budget')) ?>" placeholder="e.g. 400000">
        </label>

        <label class="field">
            <span>Tell us about the project *</span>
            <textarea name="brief" rows="6" required placeholder="Format, duration, deadline, references…"><?= e((string) old('brief')) ?></textarea>
        </label>

        <button class="btn btn--primary btn--block" type="submit">Send the brief</button>
        <p class="form-note">We reply within one working day. Your details stay with the studio.</p>
    </form>
</section>
