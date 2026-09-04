<?php

namespace App\Models;

use App\Support\Database;

class Package
{
    public static function all(): array
    {
        return array_map([self::class, 'hydrate'], Database::all(
            'SELECT * FROM packages ORDER BY sort_order ASC, id ASC'
        ));
    }

    public static function active(): array
    {
        return array_map([self::class, 'hydrate'], Database::all(
            'SELECT * FROM packages WHERE is_active = 1 ORDER BY sort_order ASC, id ASC'
        ));
    }

    public static function find(int $id): ?array
    {
        $row = Database::first('SELECT * FROM packages WHERE id = ?', [$id]);

        return $row ? self::hydrate($row) : null;
    }

    public static function findBySlug(string $slug): ?array
    {
        $slug = trim($slug);
        if ($slug === '') {
            return null;
        }

        $row = Database::first('SELECT * FROM packages WHERE slug = ? AND is_active = 1', [$slug]);

        return $row ? self::hydrate($row) : null;
    }

    public static function create(array $data): int
    {
        $data['features'] = self::encodeFeatures($data['features'] ?? []);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        if (!isset($data['sort_order']) || $data['sort_order'] === '') {
            $data['sort_order'] = (int) Database::value('SELECT COALESCE(MAX(sort_order), 0) + 1 FROM packages', [], 1);
        }

        if (empty($data['slug'])) {
            $data['slug'] = self::uniqueSlug((string) ($data['name'] ?? 'package'));
        }

        return Database::insert('packages', $data);
    }

    public static function update(int $id, array $data): void
    {
        if (array_key_exists('features', $data)) {
            $data['features'] = self::encodeFeatures($data['features']);
        }

        $data['updated_at'] = date('Y-m-d H:i:s');
        Database::update('packages', $data, 'id = :id', ['id' => $id]);
    }

    public static function delete(int $id): void
    {
        Database::run('UPDATE bookings SET package_id = NULL WHERE package_id = ?', [$id]);
        Database::delete('packages', 'id = ?', [$id]);
    }

    public static function move(int $id, string $direction): bool
    {
        return \App\Models\TeamMember::swapOrder('packages', $id, $direction);
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = slugify($name);
        $slug = $base;
        $i = 2;

        while (self::slugTaken($slug, $ignoreId)) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    private static function slugTaken(string $slug, ?int $ignoreId): bool
    {
        $sql = 'SELECT id FROM packages WHERE slug = ?';
        $params = [$slug];

        if ($ignoreId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $ignoreId;
        }

        return Database::first($sql, $params) !== null;
    }

    public static function encodeFeatures(mixed $features): string
    {
        if (is_string($features)) {
            $lines = array_values(array_filter(array_map('trim', preg_split('/\R/', $features) ?: [])));

            return json_encode($lines, JSON_UNESCAPED_UNICODE) ?: '[]';
        }

        if (!is_array($features)) {
            return '[]';
        }

        $clean = [];
        foreach ($features as $feature) {
            $feature = trim((string) $feature);
            if ($feature !== '') {
                $clean[] = $feature;
            }
        }

        return json_encode($clean, JSON_UNESCAPED_UNICODE) ?: '[]';
    }

    public static function decodeFeatures(mixed $features): array
    {
        if (is_array($features)) {
            return array_values(array_filter(array_map('strval', $features)));
        }

        if (!is_string($features) || trim($features) === '') {
            return [];
        }

        $decoded = json_decode($features, true);

        return is_array($decoded)
            ? array_values(array_filter(array_map('strval', $decoded)))
            : [];
    }

    private static function hydrate(array $row): array
    {
        $row['features'] = self::decodeFeatures($row['features'] ?? '[]');

        return $row;
    }
}
