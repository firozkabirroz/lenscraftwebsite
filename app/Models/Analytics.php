<?php

namespace App\Models;

use App\Support\Database;

class Analytics
{
    public static function trackPageView(string $path): void
    {
        if (!Database::tableExists('page_views')) {
            return;
        }

        $referrer = $_SERVER['HTTP_REFERER'] ?? '';

        Database::insert('page_views', [
            'path'       => mb_substr($path, 0, 200),
            'referrer'   => mb_substr($referrer, 0, 255),
            'source'     => self::sourceFrom($referrer),
            'ip_hash'    => sha1(($_SERVER['REMOTE_ADDR'] ?? '') . date('Y-m-d')),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private static function sourceFrom(string $referrer): string
    {
        if ($referrer === '') {
            return 'direct';
        }
        $host = strtolower((string) parse_url($referrer, PHP_URL_HOST));

        return match (true) {
            str_contains($host, 'instagram') => 'instagram',
            str_contains($host, 'facebook')  => 'facebook',
            str_contains($host, 'google')    => 'google',
            str_contains($host, 'youtube')   => 'youtube',
            $host === ''                     => 'direct',
            default                          => 'referral',
        };
    }

    public static function summary(int $days = 30): array
    {
        $since = date('Y-m-d 00:00:00', strtotime('-' . $days . ' days'));
        $prevSince = date('Y-m-d 00:00:00', strtotime('-' . ($days * 2) . ' days'));

        $visits = (int) Database::value('SELECT COUNT(*) FROM page_views WHERE created_at >= ?', [$since], 0);
        $prevVisits = (int) Database::value('SELECT COUNT(*) FROM page_views WHERE created_at >= ? AND created_at < ?', [$prevSince, $since], 0);
        $videoViews = (int) Database::value('SELECT COALESCE(SUM(views), 0) FROM videos', [], 0);
        $bookings = (int) Database::value('SELECT COUNT(*) FROM bookings WHERE created_at >= ?', [$since], 0);

        return [
            'visits'        => $visits,
            'visits_change' => self::change($visits, $prevVisits),
            'video_views'   => $videoViews,
            'bookings'      => $bookings,
            'booking_rate'  => $visits > 0 ? round($bookings / $visits * 100, 1) : 0.0,
        ];
    }

    private static function change(int $current, int $previous): float
    {
        if ($previous === 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round(($current - $previous) / $previous * 100, 1);
    }

    /** Weekly video view counts for the bar chart. */
    public static function weeklyVideoViews(int $weeks = 12): array
    {
        $rows = Database::all(
            'SELECT YEARWEEK(created_at, 3) AS yw, COUNT(*) AS total
             FROM video_views WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? WEEK)
             GROUP BY yw ORDER BY yw ASC',
            [$weeks]
        );

        $series = [];
        foreach ($rows as $i => $row) {
            $series['W' . ($i + 1)] = (int) $row['total'];
        }

        if ($series === []) {
            // No tracked plays yet — fall back to a flat series so the chart still renders.
            for ($i = 1; $i <= $weeks; $i++) {
                $series['W' . $i] = 0;
            }
        }

        return $series;
    }

    public static function weeklyVisits(int $weeks = 12): array
    {
        $rows = Database::all(
            'SELECT DATE(created_at) AS d, COUNT(*) AS total
             FROM page_views WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
             GROUP BY d ORDER BY d ASC',
            [$weeks * 7]
        );

        $series = [];
        foreach ($rows as $row) {
            $series[date('M d', strtotime($row['d']))] = (int) $row['total'];
        }

        return $series;
    }

    public static function sources(): array
    {
        return Database::all(
            'SELECT COALESCE(NULLIF(source, ""), "direct") AS source, COUNT(*) AS total
             FROM page_views GROUP BY source ORDER BY total DESC LIMIT 8'
        );
    }

    public static function topPages(int $limit = 6): array
    {
        return Database::all(
            'SELECT path, COUNT(*) AS total FROM page_views GROUP BY path ORDER BY total DESC LIMIT ' . max(1, min($limit, 20))
        );
    }
}
