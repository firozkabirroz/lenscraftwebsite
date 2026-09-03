<?php

namespace App\Models;

use App\Support\Database;

class Video
{
    public const CATEGORIES = ['Hero Reel', 'Documentary', 'Commercial', 'Film & Natok', 'Corporate AV', 'Events'];

    public static function filtered(string $status = '', string $category = '', string $search = ''): array
    {
        $sql = 'SELECT v.*, m.path AS poster_path, p.title AS project_title
                FROM videos v
                LEFT JOIN media m ON m.id = v.poster_media_id
                LEFT JOIN projects p ON p.id = v.project_id
                WHERE 1 = 1';
        $params = [];

        if ($status !== '') {
            $sql .= ' AND v.status = ?';
            $params[] = $status;
        }
        if ($category !== '' && $category !== 'All') {
            $sql .= ' AND v.category = ?';
            $params[] = $category;
        }
        if ($search !== '') {
            $sql .= ' AND v.title LIKE ?';
            $params[] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY v.created_at DESC';

        return Database::all($sql, $params);
    }

    public static function find(int $id): ?array
    {
        return Database::first(
            'SELECT v.*, m.path AS poster_path FROM videos v LEFT JOIN media m ON m.id = v.poster_media_id WHERE v.id = ?',
            [$id]
        );
    }

    public static function heroReel(): ?array
    {
        return Database::first(
            'SELECT v.*, m.path AS poster_path FROM videos v LEFT JOIN media m ON m.id = v.poster_media_id
             WHERE v.is_published = 1 AND v.place_home_hero = 1 ORDER BY v.updated_at DESC LIMIT 1'
        );
    }

    public static function forWorkGrid(int $limit = 8): array
    {
        return Database::all(
            'SELECT v.*, m.path AS poster_path FROM videos v LEFT JOIN media m ON m.id = v.poster_media_id
             WHERE v.is_published = 1 AND v.place_work_grid = 1 ORDER BY v.updated_at DESC LIMIT ' . max(1, min($limit, 24))
        );
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        return Database::insert('videos', $data);
    }

    public static function update(int $id, array $data): void
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        Database::update('videos', $data, 'id = :id', ['id' => $id]);
    }

    public static function delete(int $id): void
    {
        Database::delete('video_views', 'video_id = ?', [$id]);
        Database::delete('videos', 'id = ?', [$id]);
    }

    public static function registerView(int $id): void
    {
        Database::run('UPDATE videos SET views = views + 1 WHERE id = ?', [$id]);
        Database::insert('video_views', ['video_id' => $id, 'created_at' => date('Y-m-d H:i:s')]);
    }

    public static function stats(): array
    {
        return [
            'total'      => (int) Database::value('SELECT COUNT(*) FROM videos', [], 0),
            'published'  => (int) Database::value('SELECT COUNT(*) FROM videos WHERE is_published = 1', [], 0),
            'processing' => (int) Database::value('SELECT COUNT(*) FROM videos WHERE status = "processing"', [], 0),
            'views'      => (int) Database::value('SELECT COALESCE(SUM(views), 0) FROM videos', [], 0),
            'storage'    => (int) Database::value('SELECT COALESCE(SUM(size_bytes), 0) FROM videos', [], 0),
        ];
    }

    public static function top(int $limit = 5): array
    {
        return Database::all('SELECT title, views FROM videos ORDER BY views DESC LIMIT ' . max(1, min($limit, 20)));
    }
}
