<?php
/**
 * Global Helper Functions
 * 全局辅助函数
 */

/**
 * Get App instance or service from container
 * 获取 App 实例或服务
 */
function app($service = null) {
    $app = App\Core\App::getInstance();
    return $service ? $app->get($service) : $app;
}

/**
 * Get configuration value
 * 快捷访问配置
 */
function config($key, $default = null) {
    return app('config')->get($key, $default);
}

/**
 * Get database instance
 * 快捷访问数据库
 */
function db() {
    return app('db');
}

/**
 * Render view template
 * 快捷渲染视图
 */
function view($template, $data = []) {
    return app('view')->render($template, $data);
}

/**
 * Generate URL
 * 生成 URL
 */
function url($path = '') {
    $base = config('app.url', '');
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

/**
 * Escape output for HTML
 * 转义 HTML 输出
 */
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Dump and die - for debugging
 * 调试输出并终止
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
 * 获取当前请求实例
 */
function request() {
    return app('request');
}

/**
 * Redirect to URL
 * 重定向
 */
function redirect($url, $code = 302) {
    header("Location: $url", true, $code);
    exit;
}

/**
 * Get or set session value
 * Session 辅助函数
 */
function session($key = null, $default = null) {
    if ($key === null) {
        return $_SESSION ?? [];
    }
    return $_SESSION[$key] ?? $default;
}

/**
 * Get old input value (for form repopulation)
 * 获取旧输入值（表单回显）
 */
function old($key, $default = '') {
    return $_SESSION['_old'][$key] ?? $default;
}

/**
 * Get CSRF token
 * 获取 CSRF Token
 */
function csrf_token() {
    if (!isset($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

/**
 * Generate CSRF hidden input field
 * 生成 CSRF 隐藏字段
 */
function csrf_field() {
    return '<input type="hidden" name="_csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Trigger action hook
 * 触发动作钩子
 */
function do_action($hook, ...$args) {
    return app('hook')->trigger($hook, ...$args);
}

/**
 * Apply filter hook
 * 应用过滤钩子
 */
function apply_filter($hook, $value) {
    return app('hook')->filter($hook, $value);
}

/**
 * Get asset URL
 * 获取资源 URL
 */
function asset($path) {
    return url('assets/' . ltrim($path, '/'));
}

/**
 * Get theme asset URL
 * 获取主题资源 URL
 */
function theme_asset($path) {
    $theme = config('app.theme', 'default');
    return url("themes/$theme/assets/" . ltrim($path, '/'));
}

/**
 * Check if user is logged in
 * 检查用户是否登录
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current authenticated user
 * 获取当前登录用户
 */
function current_user() {
    return $_SESSION['user'] ?? null;
}

/**
 * Return JSON response
 * 返回 JSON 响应
 */
function json($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Get base path
 * 获取基础路径
 */
function base_path($path = '') {
    return __DIR__ . '/' . ltrim($path, '/');
}

/**
 * Get storage path
 * 获取存储路径
 */
function storage_path($path = '') {
    return base_path('storage/' . ltrim($path, '/'));
}

/**
 * Check if request is HTMX request
 * 检查是否为 HTMX 请求
 */
function is_htmx() {
    return isset($_SERVER['HTTP_HX_REQUEST']);
}

/**
 * Flash message to session
 * 闪存消息到 Session
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
 * 中止并返回 HTTP 错误
 */
function abort($code = 404, $message = '') {
    http_response_code($code);
    if ($message) {
        echo $message;
    }
    exit;
}
