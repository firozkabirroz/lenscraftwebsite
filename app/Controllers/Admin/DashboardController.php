<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Analytics;
use App\Models\Booking;
use App\Models\Client;
use App\Models\MediaAsset;
use App\Models\Message;
use App\Models\Project;
use App\Models\Video;
use App\Support\Activity;
use App\Support\Database;

class DashboardController extends Controller
{
    public function index(): void
    {
        $bookings = Booking::stats();
        $videos = Video::stats();

        $this->admin('admin.dashboard', [
            'pageTitle'    => 'Dashboard',
            'pageSubtitle' => 'Overview of studio activity',
            'actionLabel'  => 'New Upload',
            'actionUrl'    => url('/admin/videos/upload'),
            'activeNav'    => 'dashboard',
            'stats'        => [
                ['label' => 'OPEN BOOKINGS', 'value' => (string) $bookings['open'], 'meta' => $bookings['by_status']['confirmed'] . ' confirmed'],
                ['label' => 'UNREAD MESSAGES', 'value' => (string) Message::unreadCount(), 'meta' => 'from the contact form'],
                ['label' => 'PUBLISHED VIDEOS', 'value' => (string) $videos['published'], 'meta' => number_format($videos['views']) . ' total views'],
                ['label' => 'PROJECTS LIVE', 'value' => (string) Project::counts()['published'], 'meta' => Project::counts()['draft'] . ' in draft'],
            ],
            'recentBookings' => Booking::recent(5),
            'recentVideos'   => Database::all('SELECT * FROM videos ORDER BY created_at DESC LIMIT 5'),
            'messages'       => Message::filtered('unread')[0] ?? null,
            'unreadList'     => array_slice(Message::filtered('unread'), 0, 4),
            'activity'       => Activity::recent(6),
            'summary'        => Analytics::summary(),
            'clients'        => Client::count(),
            'storage'        => MediaAsset::stats(),
        ]);
    }
}
