<?php

namespace App\Models;

use App\Support\Database;

class Client
{
    public static function all(string $search = ''): array
    {
        $sql = 'SELECT c.*,
                       (SELECT COUNT(*) FROM bookings b WHERE b.client_id = c.id) AS booking_count,
                       (SELECT COALESCE(SUM(b.quote_total), 0) FROM bookings b WHERE b.client_id = c.id AND b.status IN ("confirmed","completed")) AS revenue
                FROM clients c WHERE 1 = 1';
        $params = [];

        if ($search !== '') {
            $sql .= ' AND (c.name LIKE ? OR c.organisation LIKE ?)';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY c.name ASC';

        return Database::all($sql, $params);
    }

    public static function find(int $id): ?array
    {
        return Database::first('SELECT * FROM clients WHERE id = ?', [$id]);
    }

    public static function findOrCreate(string $name, string $organisation = '', string $email = '', string $phone = ''): int
    {
        $existing = Database::first(
            'SELECT id FROM clients WHERE (email <> "" AND email = ?) OR name = ? LIMIT 1',
            [$email, $name]
        );

        if ($existing) {
            return (int) $existing['id'];
        }

        return Database::insert('clients', [
            'name'         => $name,
            'organisation' => $organisation,
            'email'        => $email,
            'phone'        => $phone,
            'created_at'   => date('Y-m-d H:i:s'),
        ]);
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');

        return Database::insert('clients', $data);
    }

    public static function update(int $id, array $data): void
    {
        Database::update('clients', $data, 'id = :id', ['id' => $id]);
    }

    public static function delete(int $id): void
    {
        Database::run('UPDATE bookings SET client_id = NULL WHERE client_id = ?', [$id]);
        Database::delete('clients', 'id = ?', [$id]);
    }

    public static function count(): int
    {
        return (int) Database::value('SELECT COUNT(*) FROM clients', [], 0);
    }
}
