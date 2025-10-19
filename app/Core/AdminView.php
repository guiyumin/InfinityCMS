<?php

namespace App\Core;

/**
 * Admin View
 *
 * Dedicated view renderer for admin/dashboard pages.
 * Admin pages are core infrastructure and should NOT be part of themes.
 * Themes only handle public-facing pages.
 *
 * Architecture:
 * - Admin views: app/Views/admin/
 * - Public views: themes/{theme}/pages/
 */
class AdminView {
    /**
     * Admin views base path
     * @var string
     */
    protected $viewsPath;

    /**
     * Shared view data
     * @var array
     */
    protected $shared = [];

    /**
     * Constructor
     */
    public function __construct() {
        $this->viewsPath = base_path('app/Views/admin');
    }

    /**
     * Render an admin view
     *
     * @param string $template Template name (e.g., 'dashboard.index', 'migrations.index')
     * @param array $data Data to pass to view
     * @param string|null $layout Layout name (default: 'admin')
     * @return string
     */
    public function render($template, array $data = [], $layout = null) {
        // Merge with shared data
        $data = array_merge($this->shared, $data);

        // Check if HTMX request (partial rendering)
        if (is_htmx() && $layout === null) {
            return $this->renderPartial($template, $data);
        }

        // Render full page with admin layout
        $layout = $layout ?? 'admin';
        return $this->renderWithLayout($template, $data, $layout);
    }

    /**
     * Render partial template (without layout)
     *
     * @param string $template
     * @param array $data
     * @return string
     */
    public function renderPartial($template, array $data = []) {
        $templatePath = $this->findTemplate($template);

        if (!$templatePath) {
            throw new \Exception("Admin template '{$template}' not found");
        }

        return $this->renderFile($templatePath, $data);
    }

    /**
     * Render template with layout
     *
     * @param string $template
     * @param array $data
     * @param string $layout
     * @return string
     */
    protected function renderWithLayout($template, array $data, $layout) {
        // Render main content
        $content = $this->renderPartial($template, $data);

        // Render layout with content
        $layoutPath = $this->viewsPath . "/layouts/{$layout}.php";

        if (!file_exists($layoutPath)) {
            throw new \Exception("Admin layout '{$layout}' not found");
        }

        $data['content'] = $content;
        return $this->renderFile($layoutPath, $data);
    }

    /**
     * Render a file with data
     *
     * @param string $path
     * @param array $data
     * @return string
     */
    protected function renderFile($path, array $data = []) {
        // Validate path is within admin views directory
        $this->validatePath($path);

        // Create admin context (simpler than ThemeContext, admin is trusted)
        $admin = new AdminContext($data, $this->shared, $this);

        // Extract data for convenience (admin templates are trusted)
        extract($data, EXTR_SKIP);

        // Start output buffering
        ob_start();

        // Include template file
        include $path;

        // Return buffered content
        return ob_get_clean();
    }

    /**
     * Find template file
     *
     * @param string $template
     * @return string|null
     */
    protected function findTemplate($template) {
        // Security: Remove any directory traversal attempts
        $template = str_replace(['..', "\0"], '', $template);

        // Convert dot notation to path (dashboard.index -> dashboard/index)
        $template = str_replace('.', '/', $template);

        // Full path
        $path = $this->viewsPath . "/{$template}.php";

        if (file_exists($path) && $this->isPathSafe($path)) {
            return $path;
        }

        return null;
    }

    /**
     * Include a partial
     *
     * @param string $partial
     * @param array $data
     * @return void
     */
    public function partial($partial, array $data = []) {
        // Security: Remove any directory traversal attempts
        $partial = str_replace(['..', "\0"], '', $partial);

        $partialPath = $this->viewsPath . "/partials/{$partial}.php";

        if (file_exists($partialPath) && $this->isPathSafe($partialPath)) {
            // Create admin context
            $admin = new AdminContext($data, $this->shared, $this);

            // Extract data for convenience
            extract($data, EXTR_SKIP);

            include $partialPath;
        }
    }

    /**
     * Share data with all views
     *
     * @param string|array $key
     * @param mixed $value
     * @return void
     */
    public function share($key, $value = null) {
        if (is_array($key)) {
            $this->shared = array_merge($this->shared, $key);
        } else {
            $this->shared[$key] = $value;
        }
    }

    /**
     * Get admin asset URL
     *
     * @param string $path
     * @return string
     */
    public function asset($path) {
        return url('assets/admin/' . ltrim($path, '/'));
    }

    /**
     * Check if view exists
     *
     * @param string $template
     * @return bool
     */
    public function exists($template) {
        return $this->findTemplate($template) !== null;
    }

    /**
     * Validate that a path is within admin views directory
     *
     * @param string $path
     * @return bool
     */
    protected function isPathSafe($path) {
        $realPath = realpath($path);
        if ($realPath === false) {
            return false;
        }

        $realViewsPath = realpath($this->viewsPath);
        if ($realViewsPath === false) {
            return false;
        }

        return str_starts_with($realPath, $realViewsPath);
    }

    /**
     * Validate path before rendering
     *
     * @param string $path
     * @return void
     * @throws \Exception
     */
    protected function validatePath($path) {
        if (!$this->isPathSafe($path)) {
            throw new \Exception("Invalid admin template path: Path must be within admin views directory");
        }
    }
}

/**
 * Admin Context
 *
 * Helper object for admin templates with convenient methods.
 * Simpler than ThemeContext since admin templates are trusted code.
 */
class AdminContext {
    protected $data = [];
    protected $shared = [];
    protected $view;

    public function __construct(array $data = [], array $shared = [], ?AdminView $view = null) {
        $this->data = $data;
        $this->shared = $shared;
        $this->view = $view;
    }

    /**
     * Get data value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null) {
        return $this->data[$key] ?? $this->shared[$key] ?? $default;
    }

    /**
     * Check if data exists
     *
     * @param string $key
     * @return bool
     */
    public function has($key) {
        return isset($this->data[$key]) || isset($this->shared[$key]);
    }

    /**
     * Escape HTML
     *
     * @param string $value
     * @return string
     */
    public function e($value) {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
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
     * Get admin asset URL
     *
     * @param string $path
     * @return string
     */
    public function asset($path) {
        return url('assets/admin/' . ltrim($path, '/'));
    }

    /**
     * Render partial
     *
     * @param string $name
     * @param array $data
     * @return void
     */
    public function partial($name, array $data = []) {
        if ($this->view) {
            $this->view->partial($name, $data);
        }
    }

    /**
     * CSRF field
     *
     * @return string
     */
    public function csrfField() {
        return csrf_field();
    }

    /**
     * Check if logged in
     *
     * @return bool
     */
    public function isLoggedIn() {
        return is_logged_in();
    }

    /**
     * Get current user
     *
     * @return array|null
     */
    public function currentUser() {
        return current_user();
    }

    /**
     * Magic getter
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key) {
        return $this->get($key);
    }

    /**
     * Magic isset
     *
     * @param string $key
     * @return bool
     */
    public function __isset($key) {
        return $this->has($key);
    }
}
