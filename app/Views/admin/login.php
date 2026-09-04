<form class="auth-card" method="post" action="<?= url('/admin/login') ?>">
    <?= csrf_field() ?>
    <img src="<?= asset('img/logo.svg') ?>" alt="" width="56" height="56">
    <h1>Admin Login</h1>
    <p class="auth-card__sub">LensCraft Production studio panel</p>

    <label class="field">
        <span>Email</span>
        <input type="email" name="email" value="<?= e((string) old('email')) ?>" required autofocus autocomplete="username">
    </label>

    <label class="field">
        <span>Password</span>
        <input type="password" name="password" required autocomplete="current-password">
    </label>

    <div class="auth-card__row">
        <label class="switch">
            <input type="checkbox" name="remember" checked>
            <i></i>
            <span>Keep me signed in</span>
        </label>
        <a href="<?= url('/contact') ?>">Need help?</a>
    </div>

    <button class="btn btn--primary btn--block" type="submit">Sign In</button>
    <p class="auth-card__foot">Protected area · passwords are stored only in the database</p>
</form>
