<?php

namespace App\Core;

/**
 * Theme Context
 *
 * Provides a secure, controlled environment for theme templates.
 * Themes access data and functionality through this object instead of
 * having direct access to global functions and services.
 *
 * Security Features:
 * - No direct database access
 * - No arbitrary service container access
 * - Automatic HTML escaping by default
 * - Controlled configuration access (no sensitive data)
 * - Path validation for includes
 */
class ThemeContext {
    /**
     * Template data
     * @var array
     */
    protected $data = [];

    /**
     * Shared data from View instance
     * @var array
     */
    protected $shared = [];

    /**
     * View instance for rendering partials
     * @var View
     */
    protected $view;

    /**
     * Constructor
     *
     * @param array $data Template data
     * @param array $shared Shared data
     * @param View $view View instance
     */
    public function __construct(array $data = [], array $shared = [], ?View $view = null) {
        $this->data = $data;
        $this->shared = $shared;
        $this->view = $view ?? app('view');
    }

    /**
     * Get data value with optional escaping
     *
     * @param string $key Data key
     * @param mixed $default Default value
     * @param bool $escape Whether to escape HTML (default: true)
     * @return mixed
     */
    public function get($key, $default = null, $escape = true) {
        $value = $this->data[$key] ?? $this->shared[$key] ?? $default;

        if ($escape && is_string($value)) {
            return $this->escape($value);
        }

        return $value;
    }

    /**
     * Get raw (unescaped) data value
     * Use with caution - only for trusted content
     *
     * @param string $key Data key
     * @param mixed $default Default value
     * @return mixed
     */
    public function raw($key, $default = null) {
        return $this->get($key, $default, false);
    }

    /**
     * Check if data key exists
     *
     * @param string $key
     * @return bool
     */
    public function has($key) {
        return isset($this->data[$key]) || isset($this->shared[$key]);
    }

    /**
     * Escape HTML for safe output
     *
     * @param string $value
     * @return string
     */
    public function escape($value) {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }

    /**
     * Alias for escape() - shorter syntax
     *
     * @param string $value
     * @return string
     */
    public function e($value) {
        return $this->escape($value);
    }

    /**
     * Generate URL
     *
     * @param string $path
     * @return string
     */
    public function url($path = '') {
        return url($path);
    }

    /**
     * Generate theme asset URL
     *
     * @param string $path
     * @return string
     */
    public function asset($path) {
        return theme_asset($path);
    }

    /**
     * Get safe configuration values
     * Blocks access to sensitive config like database credentials
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function config($key, $default = null) {
        // Whitelist of safe config keys
        $allowedPrefixes = ['app.', 'theme.'];
        $blockedKeys = ['app.debug', 'database.', 'session.'];

        // Check if key is blocked
        foreach ($blockedKeys as $blocked) {
            if (str_starts_with($key, $blocked)) {
                return $default;
            }
        }

        // Check if key is in allowed list
        foreach ($allowedPrefixes as $allowed) {
            if (str_starts_with($key, $allowed)) {
                return config($key, $default);
            }
        }

        return $default;
    }

    /**
     * Check if user is logged in
     *
     * @return bool
     */
    public function isLoggedIn() {
        return is_logged_in();
    }

    /**
     * Get current user data (safe fields only)
     *
     * @return array|null
     */
    public function currentUser() {
        $user = current_user();

        if (!$user) {
            return null;
        }

        // Return only safe fields
        return [
            'id' => $user['id'] ?? null,
            'username' => $user['username'] ?? null,
            'email' => $user['email'] ?? null,
        ];
    }

    /**
     * Render a partial template
     *
     * @param string $name Partial name
     * @param array $data Additional data
     * @return void
     */
    public function partial($name, array $data = []) {
        // Merge with current data
        $mergedData = array_merge($this->data, $this->shared, $data);

        $this->view->partial($name, $mergedData);
    }

    /**
     * Check if request is HTMX
     *
     * @return bool
     */
    public function isHtmx() {
        return is_htmx();
    }

    /**
     * Get CSRF token field
     *
     * @return string
     */
    public function csrfField() {
        return csrf_field();
    }

    /**
     * Get flash message
     *
     * @param string $key
     * @return mixed
     */
    public function flash($key) {
        return flash($key);
    }

    /**
     * Check if current URI matches pattern
     *
     * @param string $pattern
     * @return bool
     */
    public function uriIs($pattern) {
        $request = app('request');
        $uri = $request->uri();

        // Exact match
        if ($uri === $pattern) {
            return true;
        }

        // Starts with match (for admin pages, etc.)
        if (str_ends_with($pattern, '*')) {
            $prefix = rtrim($pattern, '*');
            return str_starts_with($uri, $prefix);
        }

        return false;
    }

    /**
     * Magic getter for data access
     * Automatically escapes by default
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key) {
        return $this->get($key);
    }

    /**
     * Magic isset for data checking
     *
     * @param string $key
     * @return bool
     */
    public function __isset($key) {
        return $this->has($key);
    }

    /**
     * Make data accessible as array (for extract-style access)
     * Returns data WITHOUT escaping for compatibility
     *
     * @return array
     */
    public function toArray() {
        return array_merge($this->shared, $this->data);
    }

    /**
     * Get all variable names (for extract-style access)
     *
     * @return array
     */
    public function getVariables() {
        return array_keys($this->toArray());
    }
}
