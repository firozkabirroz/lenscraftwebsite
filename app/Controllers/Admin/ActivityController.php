<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Support\Activity;

class ActivityController extends Controller
{
    public function index(): void
    {
        $filter = (string) request('type', '');

        $this->admin('admin.activity', [
            'pageTitle'    => 'Activity',
            'pageSubtitle' => 'Who changed what, and when',
            'actionLabel'  => 'Back to Settings',
            'actionUrl'    => url('/admin/settings'),
            'activeNav'    => 'settings',
            'entries'      => Activity::recent(60, $filter),
            'filter'       => $filter,
            'filters'      => ['' => 'All activity', 'video' => 'Uploads', 'booking' => 'Bookings', 'content' => 'Content', 'message' => 'Messages', 'session' => 'Logins'],
        ]);
    }
}
