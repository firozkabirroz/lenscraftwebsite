<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Support\Activity;
use App\Support\Auth;
use App\Support\Database;
use App\Support\Settings;

class SettingController extends Controller
{
    private const KEYS = [
        'studio_name', 'tagline', 'email', 'phone', 'whatsapp', 'address', 'hours',
        'instagram', 'youtube', 'facebook', 'meta_description', 'footer_note',
    ];

    public function index(): void
    {
        $this->admin('admin.settings', [
            'pageTitle'    => 'Settings',
            'pageSubtitle' => 'Studio profile, notifications and admin access',
            'actionLabel'  => 'Activity Log',
            'actionUrl'    => url('/admin/activity'),
            'activeNav'    => 'settings',
            'values'       => Settings::all(),
            'users'        => Database::all('SELECT id, name, email, role, last_login_at FROM users ORDER BY role ASC, name ASC'),
            'isOwner'      => Auth::isOwner(),
        ]);
    }

    public function update(): void
    {
        $this->postGuard();

        $pairs = [];
        foreach (self::KEYS as $key) {
            $pairs[$key] = (string) input($key, Settings::get($key));
        }
        foreach (['notify_booking', 'notify_message', 'notify_upload'] as $key) {
            $pairs[$key] = $this->checkbox($key) ? '1' : '0';
        }

        Settings::putMany($pairs);
        Activity::log('updated settings', 'settings', 0, 'Studio profile');

        flash('success', 'Settings saved.');
        redirect('/admin/settings');
    }

    public function password(): void
    {
        $this->postGuard();

        $user = Auth::user();
        $current = (string) input('current_password');
        $new = (string) input('new_password');
        $confirm = (string) input('new_password_confirm');

        if (!$user || !password_verify($current, $user['password_hash'])) {
            flash('error', 'Your current password is not correct.');
            redirect('/admin/settings');
        }
        if (strlen($new) < 8) {
            flash('error', 'Use at least 8 characters for the new password.');
            redirect('/admin/settings');
        }
        if ($new !== $confirm) {
            flash('error', 'New password and confirmation do not match.');
            redirect('/admin/settings');
        }

        Database::update('users', ['password_hash' => password_hash($new, PASSWORD_BCRYPT)], 'id = :id', ['id' => $user['id']]);
        Activity::log('changed password', 'user', (int) $user['id'], $user['email']);
        Auth::forgetUserCache();

        flash('success', 'Password updated and saved to the database.');
        redirect('/admin/settings');
    }

    public function storeUser(): void
    {
        Auth::requireOwner();
        verify_csrf();

        $name = (string) input('name');
        $email = (string) input('email');
        $password = (string) input('password');
        $role = input('role') === 'owner' ? 'owner' : 'editor';

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
            flash('error', 'Name, a valid email and an 8+ character password are required.');
            redirect('/admin/settings');
        }
        if (Database::value('SELECT id FROM users WHERE email = ?', [$email])) {
            flash('error', 'That email already has an account.');
            redirect('/admin/settings');
        }

        $id = Database::insert('users', [
            'name'          => $name,
            'email'         => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'role'          => $role,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        Activity::log('added admin user', 'user', $id, $email);
        flash('success', 'Admin user created.');

        redirect('/admin/settings');
    }
}
