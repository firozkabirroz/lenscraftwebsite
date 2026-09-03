<?php

namespace App\Models;

use App\Support\Database;

class Booking
{
    public const STATUSES = ['inquiry', 'pending', 'confirmed', 'completed', 'cancelled'];
    public const TYPES = ['Documentary', 'Commercial', 'Film & Natok', 'Corporate AV', 'Events', 'Aerial / FPV'];

    public static function filtered(string $status = '', string $search = ''): array
    {
        $sql = 'SELECT * FROM bookings WHERE 1 = 1';
        $params = [];

        if ($status !== '' && $status !== 'all') {
            $sql .= ' AND status = ?';
            $params[] = $status;
        }
        if ($search !== '') {
            $sql .= ' AND (client_name LIKE ? OR code LIKE ? OR organisation LIKE ?)';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY COALESCE(shoot_date, created_at) DESC, id DESC';

        return Database::all($sql, $params);
    }

    public static function find(int $id): ?array
    {
        return Database::first('SELECT * FROM bookings WHERE id = ?', [$id]);
    }

    public static function upcoming(int $limit = 5): array
    {
        return Database::all(
            'SELECT * FROM bookings WHERE status IN ("pending", "confirmed") ORDER BY shoot_date ASC LIMIT ' . max(1, min($limit, 20))
        );
    }

    public static function recent(int $limit = 5): array
    {
        return Database::all('SELECT * FROM bookings ORDER BY created_at DESC LIMIT ' . max(1, min($limit, 20)));
    }

    public static function create(array $data): int
    {
        $data['code'] = $data['code'] ?? self::nextCode();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        return Database::insert('bookings', $data);
    }

    public static function update(int $id, array $data): void
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        Database::update('bookings', $data, 'id = :id', ['id' => $id]);
    }

    public static function delete(int $id): void
    {
        foreach (['booking_items', 'booking_crew', 'booking_events'] as $table) {
            Database::delete($table, 'booking_id = ?', [$id]);
        }
        Database::delete('bookings', 'id = ?', [$id]);
    }

    public static function nextCode(): string
    {
        $last = (int) Database::value('SELECT COALESCE(MAX(CAST(SUBSTRING(code, 4) AS UNSIGNED)), 1000) FROM bookings', [], 1000);

        return 'BK-' . ($last + 1);
    }

    public static function items(int $bookingId): array
    {
        return Database::all('SELECT * FROM booking_items WHERE booking_id = ? ORDER BY id ASC', [$bookingId]);
    }

    public static function crew(int $bookingId): array
    {
        return Database::all('SELECT * FROM booking_crew WHERE booking_id = ? ORDER BY id ASC', [$bookingId]);
    }

    public static function events(int $bookingId): array
    {
        return Database::all('SELECT * FROM booking_events WHERE booking_id = ? ORDER BY id ASC', [$bookingId]);
    }

    public static function addEvent(int $bookingId, string $label, string $note = '', bool $done = true): void
    {
        Database::insert('booking_events', [
            'booking_id'  => $bookingId,
            'label'       => $label,
            'note'        => $note,
            'is_done'     => $done ? 1 : 0,
            'happened_at' => $done ? date('Y-m-d H:i:s') : null,
        ]);
    }

    public static function replaceItems(int $bookingId, array $labels, array $amounts): float
    {
        Database::delete('booking_items', 'booking_id = ?', [$bookingId]);
        $total = 0.0;

        foreach ($labels as $i => $label) {
            $label = trim((string) $label);
            $amount = (float) str_replace(',', '', (string) ($amounts[$i] ?? 0));
            if ($label === '' && $amount <= 0) {
                continue;
            }
            Database::insert('booking_items', [
                'booking_id' => $bookingId,
                'label'      => $label !== '' ? $label : 'Line item',
                'amount'     => $amount,
            ]);
            $total += $amount;
        }

        Database::update('bookings', ['quote_total' => $total], 'id = :id', ['id' => $bookingId]);

        return $total;
    }

    public static function replaceCrew(int $bookingId, array $people, array $roles, array $days): void
    {
        Database::delete('booking_crew', 'booking_id = ?', [$bookingId]);

        foreach ($people as $i => $person) {
            $person = trim((string) $person);
            if ($person === '') {
                continue;
            }
            Database::insert('booking_crew', [
                'booking_id' => $bookingId,
                'person'     => $person,
                'role'       => trim((string) ($roles[$i] ?? 'Crew')),
                'days'       => trim((string) ($days[$i] ?? '')),
            ]);
        }
    }

    public static function stats(): array
    {
        $rows = Database::all('SELECT status, COUNT(*) AS total FROM bookings GROUP BY status');
        $byStatus = ['inquiry' => 0, 'pending' => 0, 'confirmed' => 0, 'completed' => 0, 'cancelled' => 0];

        foreach ($rows as $row) {
            $byStatus[$row['status']] = (int) $row['total'];
        }

        return [
            'by_status' => $byStatus,
            'total'     => array_sum($byStatus),
            'open'      => $byStatus['inquiry'] + $byStatus['pending'],
            'revenue'   => (float) Database::value('SELECT COALESCE(SUM(quote_total), 0) FROM bookings WHERE status IN ("confirmed", "completed")', [], 0),
        ];
    }

    public static function byType(): array
    {
        return Database::all('SELECT project_type, COUNT(*) AS total FROM bookings GROUP BY project_type ORDER BY total DESC');
    }
}
