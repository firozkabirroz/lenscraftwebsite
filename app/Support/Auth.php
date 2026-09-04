<?php

namespace App\Support;

class Auth
{
    private static ?array $cachedUser = null;
    private static bool $userLoaded = false;

    public static function attempt(string $email, string $password): bool
    {
        $user = Database::first('SELECT * FROM users WHERE email = ? LIMIT 1', [$email]);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        self::forgetUserCache();

        Database::update('users', ['last_login_at' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $user['id']]);
        Activity::log('signed in', 'session', (int) $user['id'], $_SERVER['REMOTE_ADDR'] ?? '', (int) $user['id']);

        return true;
    }

    public static function logout(): void
    {
        self::forgetUserCache();
        $_SESSION = [];
        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        if (!self::$userLoaded) {
            self::$cachedUser = Database::first('SELECT * FROM users WHERE id = ? LIMIT 1', [$_SESSION['user_id']]);
            self::$userLoaded = true;
        }

        return self::$cachedUser;
    }

    /** Clear the per-request user cache after password / profile changes. */
    public static function forgetUserCache(): void
    {
        self::$cachedUser = null;
        self::$userLoaded = false;
    }

    public static function id(): ?int
    {
        $user = self::user();

        return $user ? (int) $user['id'] : null;
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            $_SESSION['_intended'] = current_path();
            redirect('/admin/login');
        }
    }

    public static function isOwner(): bool
    {
        $user = self::user();

        return $user !== null && $user['role'] === 'owner';
    }

    public static function requireOwner(): void
    {
        self::requireLogin();

        if (!self::isOwner()) {
            abort(403, 'Only the studio owner can perform this action.');
        }
    }
}
