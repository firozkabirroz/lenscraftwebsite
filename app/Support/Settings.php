<?php

namespace App\Support;

class Settings
{
    private static ?array $cache = null;

    public static function all(): array
    {
        if (self::$cache === null) {
            self::$cache = [];
            foreach (Database::all('SELECT `k`, `v` FROM settings') as $row) {
                self::$cache[$row['k']] = $row['v'];
            }
        }

        return self::$cache;
    }

    public static function get(string $key, string $default = ''): string
    {
        return self::all()[$key] ?? $default;
    }

    public static function put(string $key, string $value): void
    {
        Database::run(
            'INSERT INTO settings (`k`, `v`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `v` = VALUES(`v`)',
            [$key, $value]
        );
        self::$cache = null;
    }

    public static function putMany(array $pairs): void
    {
        foreach ($pairs as $key => $value) {
            self::put($key, (string) $value);
        }
    }
}
