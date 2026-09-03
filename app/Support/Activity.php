<?php

namespace App\Support;

class Activity
{
    public static function log(string $action, string $targetType = '', int $targetId = 0, string $meta = '', ?int $userId = null): void
    {
        if (!Database::tableExists('activity_log')) {
            return;
        }

        Database::insert('activity_log', [
            'user_id'     => $userId ?? Auth::id(),
            'action'      => $action,
            'target_type' => $targetType,
            'target_id'   => $targetId,
            'meta'        => mb_substr($meta, 0, 500),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    public static function recent(int $limit = 20, string $filter = ''): array
    {
        $sql = 'SELECT a.*, u.name AS user_name FROM activity_log a LEFT JOIN users u ON u.id = a.user_id';
        $params = [];

        if ($filter !== '') {
            $sql .= ' WHERE a.target_type = ?';
            $params[] = $filter;
        }

        $sql .= ' ORDER BY a.created_at DESC LIMIT ' . max(1, min($limit, 200));

        return Database::all($sql, $params);
    }
}
