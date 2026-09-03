<?php

namespace App\Models;

use App\Support\Database;
use App\Support\Uploader;

class MediaAsset
{
    public static function filtered(string $type = '', string $search = ''): array
    {
        $sql = 'SELECT * FROM media WHERE 1 = 1';
        $params = [];

        if ($type !== '' && $type !== 'All') {
            $sql .= ' AND type = ?';
            $params[] = strtolower($type);
        }
        if ($search !== '') {
            $sql .= ' AND (title LIKE ? OR filename LIKE ?)';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY created_at DESC, id DESC';

        return Database::all($sql, $params);
    }

    public static function find(int $id): ?array
    {
        return Database::first('SELECT * FROM media WHERE id = ?', [$id]);
    }

    public static function images(): array
    {
        return Database::all('SELECT * FROM media WHERE type = "image" ORDER BY created_at DESC');
    }

    public static function delete(int $id): void
    {
        $media = self::find($id);
        if (!$media) {
            return;
        }

        Uploader::deleteFile($media['path']);
        Database::delete('project_media', 'media_id = ?', [$id]);
        Database::run('UPDATE projects SET cover_media_id = NULL WHERE cover_media_id = ?', [$id]);
        Database::run('UPDATE videos SET poster_media_id = NULL WHERE poster_media_id = ?', [$id]);
        Database::delete('media', 'id = ?', [$id]);
    }

    public static function stats(): array
    {
        return [
            'count'   => (int) Database::value('SELECT COUNT(*) FROM media', [], 0),
            'storage' => (int) Database::value('SELECT COALESCE(SUM(size_bytes), 0) FROM media', [], 0),
        ];
    }
}
