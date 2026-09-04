<?php

namespace App\Models;

use App\Support\Database;

class TeamMember
{
    public static function all(): array
    {
        return Database::all(
            'SELECT t.*, m.path AS photo_path FROM team_members t
             LEFT JOIN media m ON m.id = t.photo_media_id
             ORDER BY t.sort_order ASC, t.id ASC'
        );
    }

    public static function active(): array
    {
        return Database::all(
            'SELECT t.*, m.path AS photo_path FROM team_members t
             LEFT JOIN media m ON m.id = t.photo_media_id
             WHERE t.is_active = 1 ORDER BY t.sort_order ASC, t.id ASC'
        );
    }

    public static function find(int $id): ?array
    {
        return Database::first('SELECT * FROM team_members WHERE id = ?', [$id]);
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');

        if (!isset($data['sort_order']) || $data['sort_order'] === '') {
            $data['sort_order'] = (int) Database::value('SELECT COALESCE(MAX(sort_order), 0) + 1 FROM team_members', [], 1);
        }

        return Database::insert('team_members', $data);
    }

    public static function update(int $id, array $data): void
    {
        Database::update('team_members', $data, 'id = :id', ['id' => $id]);
    }

    public static function delete(int $id): void
    {
        Database::delete('team_members', 'id = ?', [$id]);
    }

    public static function move(int $id, string $direction): bool
    {
        return self::swapOrder('team_members', $id, $direction);
    }

    /** Shared helper for sort_order tables. */
    public static function swapOrder(string $table, int $id, string $direction): bool
    {
        $allowed = ['team_members', 'projects', 'packages', 'brands'];
        if (!in_array($table, $allowed, true)) {
            return false;
        }

        $row = Database::first("SELECT id, sort_order FROM {$table} WHERE id = ?", [$id]);
        if (!$row) {
            return false;
        }

        $dir = strtolower($direction) === 'up' ? 'up' : 'down';
        $neighbor = $dir === 'up'
            ? Database::first("SELECT id, sort_order FROM {$table} WHERE sort_order < ? ORDER BY sort_order DESC, id DESC LIMIT 1", [(int) $row['sort_order']])
            : Database::first("SELECT id, sort_order FROM {$table} WHERE sort_order > ? ORDER BY sort_order ASC, id ASC LIMIT 1", [(int) $row['sort_order']]);

        if (!$neighbor) {
            return false;
        }

        Database::update($table, ['sort_order' => (int) $neighbor['sort_order']], 'id = :id', ['id' => (int) $row['id']]);
        Database::update($table, ['sort_order' => (int) $row['sort_order']], 'id = :id', ['id' => (int) $neighbor['id']]);

        return true;
    }
}
