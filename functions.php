<?php
/**
 * Global Helper Functions
 */

/**
 * Get App instance or service from container
 */
function app($service = null) {
    $app = App\Core\App::getInstance();
    return $service ? $app->get($service) : $app;
}

/**
 * Get configuration value
 */
function config($key, $default = null) {
    return app('config')->get($key, $default);
}

/**
 * Get database instance
 */
function db() {
    return app('db');
}

/**
 * Render view template
 */
function view($template, $data = [], $layout = null) {
    return app('view')->render($template, $data, $layout);
}

/**
 * Render admin view template
 */
function admin_view($template, $data = []) {
    return app('admin_view')->render($template, $data);
}

/**
 * Render setup view template
 */
function setup_view($template, $data = []) {
    return app('setup_view')->render($template, $data);
}

/**
 * Render auth view template
 */
function auth_view($template, $data = []) {
    return app('auth_view')->render($template, $data);
}

/**
 * Generate URL
 */
function url($path = '') {
    // If full URL is provided, return as-is
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }

    // Use configured URL if available, otherwise auto-detect
    $base = config('app.url', null);

    if (!$base) {
        // Auto-detect base URL from current request
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        // Get the base path from SCRIPT_NAME, excluding /public/index.php
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath = '';

        if (str_contains($scriptName, '/public/')) {
            // Extract the base path before /public/
            $basePath = substr($scriptName, 0, strpos($scriptName, '/public/'));
        }

        $base = $protocol . '://' . $host . $basePath;
    }

    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

/**
 * Escape output for HTML
 */
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Dump and die - for debugging
 */
function dd(...$vars) {
    echo '<pre>';
    foreach ($vars as $var) {
        var_dump($var);
    }
    echo '</pre>';
    die(1);
}

/**
 * Get current request instance
 */
function request() {
    return app('request');
}

/**
 * Redirect to URL
 */
function redirect($url, $code = 302) {
    header("Location: $url", true, $code);
    exit;
}

/**
 * Get or set session value
 */
function session($key = null, $default = null) {
    if ($key === null) {
        return $_SESSION ?? [];
    }
    return $_SESSION[$key] ?? $default;
}

/**
 * Get old input value (for form repopulation)
 */
function old($key, $default = '') {
    return $_SESSION['_old'][$key] ?? $default;
}

/**
 * Get CSRF token
 */
function csrf_token() {
    if (!isset($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

/**
 * Generate CSRF hidden input field
 */
function csrf_field() {
    return '<input type="hidden" name="_csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Trigger action hook
 */
function do_action($hook, ...$args) {
    return app('hook')->trigger($hook, ...$args);
}

/**
 * Apply filter hook
 */
function apply_filter($hook, $value) {
    return app('hook')->filter($hook, $value);
}

/**
 * Get asset URL
 */
function asset($path) {
    return url('assets/' . ltrim($path, '/'));
}

/**
 * Get theme asset URL
 */
function theme_asset($path) {
    $theme = config('app.theme', 'default');
    return url("assets/themes/$theme/" . ltrim($path, '/'));
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current authenticated user
 */
function current_user() {
    return $_SESSION['user'] ?? null;
}

/**
 * Return JSON response
 */
function json($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Get base path
 */
function base_path($path = '') {
    return __DIR__ . '/' . ltrim($path, '/');
}

/**
 * Get storage path
 */
function storage_path($path = '') {
    return base_path('storage/' . ltrim($path, '/'));
}

/**
 * Check if request is HTMX request
 */
function is_htmx() {
    return isset($_SERVER['HTTP_HX_REQUEST']);
}

/**
 * Flash message to session
 */
function flash($key, $message = null) {
    if ($message === null) {
        $value = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }
    $_SESSION['_flash'][$key] = $message;
}

/**
 * Abort with HTTP error
 */
function abort($code = 404, $message = '') {
    http_response_code($code);
    if ($message) {
        echo $message;
    }
    exit;
}
