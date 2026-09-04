<?php

namespace App\Models;

use App\Support\Database;

class Brand
{
    public static function all(): array
    {
        return Database::all(
            'SELECT b.*, m.path AS logo_path FROM brands b LEFT JOIN media m ON m.id = b.logo_media_id
             ORDER BY b.sort_order ASC, b.id ASC'
        );
    }

    public static function active(): array
    {
        return Database::all(
            'SELECT b.*, m.path AS logo_path FROM brands b LEFT JOIN media m ON m.id = b.logo_media_id
             WHERE b.is_active = 1 ORDER BY b.sort_order ASC, b.id ASC'
        );
    }

    public static function find(int $id): ?array
    {
        return Database::first('SELECT * FROM brands WHERE id = ?', [$id]);
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');

        if (!isset($data['sort_order']) || $data['sort_order'] === '') {
            $data['sort_order'] = (int) Database::value('SELECT COALESCE(MAX(sort_order), 0) + 1 FROM brands', [], 1);
        }

        return Database::insert('brands', $data);
    }

    public static function update(int $id, array $data): void
    {
        Database::update('brands', $data, 'id = :id', ['id' => $id]);
    }

    public static function delete(int $id): void
    {
        Database::delete('brands', 'id = ?', [$id]);
    }

    public static function move(int $id, string $direction): bool
    {
        return \App\Models\TeamMember::swapOrder('brands', $id, $direction);
    }
}
