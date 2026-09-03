<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Analytics;
use App\Models\Booking;
use App\Models\Video;

class AnalyticsController extends Controller
{
    public function index(): void
    {
        $this->admin('admin.analytics', [
            'pageTitle'    => 'Analytics',
            'pageSubtitle' => 'Site traffic, video views and booking trend',
            'actionLabel'  => 'Back to Dashboard',
            'actionUrl'    => url('/admin'),
            'activeNav'    => 'analytics',
            'summary'      => Analytics::summary(),
            'weekly'       => Analytics::weeklyVideoViews(),
            'visits'       => Analytics::weeklyVisits(4),
            'sources'      => Analytics::sources(),
            'topPages'     => Analytics::topPages(),
            'topVideos'    => Video::top(5),
            'bookingTypes' => Booking::byType(),
            'bookingStats' => Booking::stats(),
        ]);
    }
}
