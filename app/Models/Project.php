<?php

namespace App\Models;

use App\Support\Database;

class Project
{
    public const CATEGORIES = ['Documentary', 'Commercial', 'Film & Natok', 'Corporate AV', 'Events', 'Aerial / FPV'];

    public static function filtered(string $category = '', string $status = '', string $search = ''): array
    {
        $sql = 'SELECT p.*, m.path AS cover_path FROM projects p LEFT JOIN media m ON m.id = p.cover_media_id WHERE 1 = 1';
        $params = [];

        if ($category !== '' && $category !== 'All') {
            $sql .= ' AND p.category = ?';
            $params[] = $category;
        }
        if ($status !== '') {
            $sql .= ' AND p.status = ?';
            $params[] = $status;
        }
        if ($search !== '') {
            $sql .= ' AND (p.title LIKE ? OR p.client_name LIKE ?)';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY p.sort_order ASC, p.id DESC';

        return Database::all($sql, $params);
    }

    public static function published(string $category = '', int $limit = 60): array
    {
        $sql = 'SELECT p.*, m.path AS cover_path FROM projects p LEFT JOIN media m ON m.id = p.cover_media_id
                WHERE p.status = "published"';
        $params = [];

        if ($category !== '' && $category !== 'All') {
            $sql .= ' AND p.category = ?';
            $params[] = $category;
        }

        $sql .= ' ORDER BY p.sort_order ASC, p.id DESC LIMIT ' . max(1, min($limit, 100));

        return Database::all($sql, $params);
    }

    public static function homepage(int $limit = 6): array
    {
        return Database::all(
            'SELECT p.*, m.path AS cover_path FROM projects p LEFT JOIN media m ON m.id = p.cover_media_id
             WHERE p.status = "published" AND p.show_on_homepage = 1
             ORDER BY p.sort_order ASC LIMIT ' . max(1, min($limit, 24))
        );
    }

    public static function find(int $id): ?array
    {
        return Database::first('SELECT * FROM projects WHERE id = ?', [$id]);
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::first(
            'SELECT p.*, m.path AS cover_path FROM projects p LEFT JOIN media m ON m.id = p.cover_media_id
             WHERE p.slug = ? AND p.status = "published"',
            [$slug]
        );
    }

    public static function gallery(int $projectId): array
    {
        return Database::all(
            'SELECT m.* FROM project_media pm JOIN media m ON m.id = pm.media_id
             WHERE pm.project_id = ? ORDER BY pm.sort_order ASC',
            [$projectId]
        );
    }

    public static function setGallery(int $projectId, array $mediaIds): void
    {
        Database::delete('project_media', 'project_id = ?', [$projectId]);

        $order = 1;
        foreach ($mediaIds as $mediaId) {
            $mediaId = (int) $mediaId;
            if ($mediaId > 0) {
                Database::insert('project_media', [
                    'project_id' => $projectId,
                    'media_id'   => $mediaId,
                    'sort_order' => $order++,
                ]);
            }
        }
    }

    public static function create(array $data): int
    {
        $data['slug'] = self::uniqueSlug($data['slug'] ?: $data['title']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        if (!isset($data['sort_order']) || $data['sort_order'] === '') {
            $data['sort_order'] = (int) Database::value('SELECT COALESCE(MAX(sort_order), 0) + 1 FROM projects', [], 1);
        }

        return Database::insert('projects', $data);
    }

    public static function update(int $id, array $data): void
    {
        $data['slug'] = self::uniqueSlug($data['slug'] ?: $data['title'], $id);
        $data['updated_at'] = date('Y-m-d H:i:s');

        Database::update('projects', $data, 'id = :id', ['id' => $id]);
    }

    public static function delete(int $id): void
    {
        Database::delete('project_media', 'project_id = ?', [$id]);
        Database::delete('projects', 'id = ?', [$id]);
    }

    public static function incrementViews(int $id): void
    {
        Database::run('UPDATE projects SET views = views + 1 WHERE id = ?', [$id]);
    }

    public static function counts(): array
    {
        return [
            'total'     => (int) Database::value('SELECT COUNT(*) FROM projects', [], 0),
            'published' => (int) Database::value('SELECT COUNT(*) FROM projects WHERE status = "published"', [], 0),
            'draft'     => (int) Database::value('SELECT COUNT(*) FROM projects WHERE status = "draft"', [], 0),
        ];
    }

    private static function uniqueSlug(string $value, int $ignoreId = 0): string
    {
        $slug = slugify($value);
        $base = $slug;
        $i = 2;

        while (Database::value('SELECT id FROM projects WHERE slug = ? AND id <> ?', [$slug, $ignoreId])) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
