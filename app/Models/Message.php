<?php

namespace App\Models;

use App\Support\Database;

class Message
{
    public static function filtered(string $status = '', string $search = ''): array
    {
        $sql = 'SELECT * FROM messages WHERE 1 = 1';
        $params = [];

        if ($status !== '' && $status !== 'all') {
            $sql .= ' AND status = ?';
            $params[] = $status;
        }
        if ($search !== '') {
            $sql .= ' AND (name LIKE ? OR subject LIKE ? OR body LIKE ?)';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY created_at DESC';

        return Database::all($sql, $params);
    }

    public static function find(int $id): ?array
    {
        return Database::first('SELECT * FROM messages WHERE id = ?', [$id]);
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');

        return Database::insert('messages', $data);
    }

    public static function markRead(int $id): void
    {
        Database::run('UPDATE messages SET status = "read" WHERE id = ? AND status = "unread"', [$id]);
    }

    public static function setStatus(int $id, string $status): void
    {
        Database::update('messages', ['status' => $status], 'id = :id', ['id' => $id]);
    }

    public static function delete(int $id): void
    {
        Database::delete('message_replies', 'message_id = ?', [$id]);
        Database::delete('messages', 'id = ?', [$id]);
    }

    public static function replies(int $messageId): array
    {
        return Database::all(
            'SELECT r.*, u.name AS user_name FROM message_replies r LEFT JOIN users u ON u.id = r.user_id
             WHERE r.message_id = ? ORDER BY r.created_at ASC',
            [$messageId]
        );
    }

    public static function addReply(int $messageId, ?int $userId, string $body): int
    {
        return Database::insert('message_replies', [
            'message_id' => $messageId,
            'user_id'    => $userId,
            'body'       => $body,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function unreadCount(): int
    {
        return (int) Database::value('SELECT COUNT(*) FROM messages WHERE status = "unread"', [], 0);
    }
}
