<?php

namespace App\Controllers;

use App\Models\Analytics;
use App\Support\Auth;
use App\Support\Settings;

abstract class Controller
{
    protected function site(string $template, array $data = []): void
    {
        Analytics::trackPageView(current_path());

        $data['settings'] = Settings::all();
        $data['navPath'] = current_path();

        render($template, $data, 'site');
    }

    protected function admin(string $template, array $data = []): void
    {
        Auth::requireLogin();

        $data['user'] = Auth::user();
        $data['settings'] = Settings::all();

        render($template, $data, 'admin');
    }

    protected function guard(): void
    {
        Auth::requireLogin();
    }

    protected function postGuard(): void
    {
        Auth::requireLogin();
        verify_csrf();
    }

    protected function checkbox(string $key): int
    {
        return !empty($_POST[$key]) ? 1 : 0;
    }

    protected function intOrNull(string $key): ?int
    {
        $value = $_POST[$key] ?? '';

        return $value === '' || $value === '0' ? null : (int) $value;
    }

    protected function dateOrNull(string $key): ?string
    {
        $value = trim((string) ($_POST[$key] ?? ''));

        return $value === '' ? null : date('Y-m-d', strtotime($value));
    }
}
