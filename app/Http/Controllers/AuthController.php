<?php

namespace App\Http\Controllers;

use App\Core\Request;

/**
 * Authentication Controller
 * Handles login, logout, and authentication
 */
class AuthController {
    /**
     * Show login page
     *
     * @return string
     */
    public function showLogin() {
        // If already logged in, redirect to admin dashboard
        if (is_logged_in()) {
            redirect(url('/admin/dashboard'));
        }

        return view('auth.login', [
            'title' => 'Login',
        ]);
    }

    /**
     * Handle login request
     *
     * @return void
     */
    public function login() {
        $request = app('request');

        // Validate CSRF token
        if (!$request->validateCsrf()) {
            flash('error', 'Invalid request. Please try again.');
            redirect(url('/login'));
            return;
        }

        // Get credentials
        $username = $request->input('username');
        $password = $request->input('password');

        // Validate input
        if (empty($username) || empty($password)) {
            flash('error', 'Please enter both username and password.');
            $_SESSION['_old'] = ['username' => $username];
            redirect(url('/login'));
            return;
        }

        // Find user
        $user = db()->table('users')
            ->where('username', $username)
            ->orWhere('email', $username)
            ->first();

        // Check if user exists and password is correct
        if (!$user || !password_verify($password, $user['password'])) {
            flash('error', 'Invalid username or password.');
            $_SESSION['_old'] = ['username' => $username];
            redirect(url('/login'));
            return;
        }

        // Check if user is active (if you have status field)
        if (isset($user['status']) && $user['status'] !== 'active') {
            flash('error', 'Your account has been deactivated.');
            redirect(url('/login'));
            return;
        }

        // Login successful - set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'] ?? 'user',
        ];

        // Clear old input
        unset($_SESSION['_old']);

        // Set flash message
        flash('success', 'Welcome back, ' . $user['username'] . '!');

        // Redirect to admin dashboard
        redirect(url('/admin/dashboard'));
    }

    /**
     * Handle logout request
     *
     * @return void
     */
    public function logout() {
        // Destroy session
        $_SESSION = [];

        // Destroy session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Destroy session file
        session_destroy();

        // Redirect to login
        flash('success', 'You have been logged out successfully.');
        redirect(url('/login'));
    }
}
