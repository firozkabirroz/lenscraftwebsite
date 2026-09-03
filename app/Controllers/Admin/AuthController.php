<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Support\Auth;
use App\Support\Settings;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            redirect('/admin');
        }

        render('admin.login', ['settings' => Settings::all()], 'auth');
    }

    public function login(): void
    {
        verify_csrf();

        $email = (string) input('email');
        $password = (string) input('password');

        if (!Auth::attempt($email, $password)) {
            remember_old(['email' => $email]);
            flash('error', 'Those credentials do not match our records.');
            redirect('/admin/login');
        }

        clear_old();
        $intended = $_SESSION['_intended'] ?? '/admin';
        unset($_SESSION['_intended']);

        redirect($intended);
    }

    public function logout(): void
    {
        Auth::logout();
        redirect('/admin/login');
    }
}
