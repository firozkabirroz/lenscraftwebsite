<?php

namespace App\Support;

class Auth
{
    public static function attempt(string $email, string $password): bool
    {
        $user = Database::first('SELECT * FROM users WHERE email = ? LIMIT 1', [$email]);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];

        Database::update('users', ['last_login_at' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $user['id']]);
        Activity::log('signed in', 'session', (int) $user['id'], $_SERVER['REMOTE_ADDR'] ?? '', (int) $user['id']);

        return true;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        static $cached = null;

        if (!self::check()) {
            return null;
        }
        if ($cached === null) {
            $cached = Database::first('SELECT * FROM users WHERE id = ? LIMIT 1', [$_SESSION['user_id']]);
        }

        return $cached;
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
