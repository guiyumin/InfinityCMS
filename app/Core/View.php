<?php

namespace App\Core;

/**
 * View - Template rendering with theme support
 * 视图类 - 支持主题的模板渲染
 */
class View {
    /**
     * Current theme name
     * @var string
     */
    protected $theme;

    /**
     * Theme base path
     * @var string
     */
    protected $themePath;

    /**
     * Shared view data
     * @var array
     */
    protected $shared = [];

    /**
     * Constructor
     *
     * @param string $theme
     */
    public function __construct($theme = 'default') {
        $this->theme = $theme;
        $this->themePath = base_path("themes/{$theme}");
    }

    /**
     * Render a view template
     * 渲染视图模板
     *
     * @param string $template Template name (e.g., 'home', 'post', 'admin.dashboard')
     * @param array $data Data to pass to view
     * @param string|null $layout Layout name (null for HTMX partial)
     * @return string
     */
    public function render($template, array $data = [], $layout = null) {
        // Merge with shared data
        $data = array_merge($this->shared, $data);

        // Check if HTMX request (partial rendering)
        if (is_htmx() && $layout === null) {
            return $this->renderPartial($template, $data);
        }

        // Render full page with layout
        $layout = $layout ?? 'base';
        return $this->renderWithLayout($template, $data, $layout);
    }

    /**
     * Render partial template (without layout)
     * 渲染局部模板（无布局）
     *
     * @param string $template
     * @param array $data
     * @return string
     */
    public function renderPartial($template, array $data = []) {
        $templatePath = $this->findTemplate($template);

        if (!$templatePath) {
            throw new \Exception("Template '{$template}' not found");
        }

        return $this->renderFile($templatePath, $data);
    }

    /**
     * Render template with layout
     * 渲染带布局的模板
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
        $layoutPath = $this->themePath . "/layouts/{$layout}.php";

        if (!file_exists($layoutPath)) {
            throw new \Exception("Layout '{$layout}' not found");
        }

        $data['content'] = $content;
        return $this->renderFile($layoutPath, $data);
    }

    /**
     * Render a file with data
     * 渲染文件
     *
     * @param string $path
     * @param array $data
     * @return string
     */
    protected function renderFile($path, array $data = []) {
        // Validate path is within theme directory
        $this->validateTemplatePath($path);

        // Create secure theme context
        $theme = new ThemeContext($data, $this->shared, $this);

        // For backward compatibility, also extract data
        // But prefix with EXTR_SKIP to prevent overwriting $theme and $path
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
     * 查找模板文件
     *
     * @param string $template
     * @return string|null
     */
    protected function findTemplate($template) {
        // Security: Remove any directory traversal attempts
        $template = str_replace(['..', "\0"], '', $template);

        // Convert dot notation to path (admin.dashboard -> admin/dashboard)
        $template = str_replace('.', '/', $template);

        // Possible locations
        $paths = [
            $this->themePath . "/pages/{$template}.php",
            $this->themePath . "/partials/{$template}.php",
            $this->themePath . "/{$template}.php",
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                // Validate the resolved path is within theme directory
                if ($this->isPathSafe($path)) {
                    return $path;
                }
            }
        }

        return null;
    }

    /**
     * Include a partial
     * 包含局部模板
     *
     * @param string $partial
     * @param array $data
     * @return void
     */
    public function partial($partial, array $data = []) {
        // Security: Remove any directory traversal attempts
        $partial = str_replace(['..', "\0"], '', $partial);

        $partialPath = $this->themePath . "/partials/{$partial}.php";

        if (file_exists($partialPath) && $this->isPathSafe($partialPath)) {
            // Create secure theme context
            $theme = new ThemeContext($data, $this->shared, $this);

            // Also extract for backward compatibility
            extract($data, EXTR_SKIP);

            include $partialPath;
        }
    }

    /**
     * Share data with all views
     * 共享数据到所有视图
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
     * Get theme asset URL
     * 获取主题资源 URL
     *
     * @param string $path
     * @return string
     */
    public function asset($path) {
        return url("themes/{$this->theme}/assets/" . ltrim($path, '/'));
    }

    /**
     * Set current theme
     * 设置当前主题
     *
     * @param string $theme
     * @return void
     */
    public function setTheme($theme) {
        $this->theme = $theme;
        $this->themePath = base_path("themes/{$theme}");
    }

    /**
     * Get current theme
     * 获取当前主题
     *
     * @return string
     */
    public function getTheme() {
        return $this->theme;
    }

    /**
     * Check if view exists
     * 检查视图是否存在
     *
     * @param string $template
     * @return bool
     */
    public function exists($template) {
        return $this->findTemplate($template) !== null;
    }

    /**
     * Validate that a path is within the theme directory
     * Security measure to prevent directory traversal
     *
     * @param string $path
     * @return bool
     */
    protected function isPathSafe($path) {
        // Get the real path (resolves symlinks and .. references)
        $realPath = realpath($path);

        if ($realPath === false) {
            return false;
        }

        // Get the real theme path
        $realThemePath = realpath($this->themePath);

        if ($realThemePath === false) {
            return false;
        }

        // Check if the resolved path starts with theme path
        return str_starts_with($realPath, $realThemePath);
    }

    /**
     * Validate template path before rendering
     * Throws exception if path is outside theme directory
     *
     * @param string $path
     * @return void
     * @throws \Exception
     */
    protected function validateTemplatePath($path) {
        if (!$this->isPathSafe($path)) {
            throw new \Exception("Invalid template path: Path must be within theme directory");
        }
    }
}
