<?php

namespace App\Core;

/**
 * Auth View
 *
 * Simple view renderer for authentication pages (login, etc).
 * Auth is core infrastructure and should NOT use themes.
 */
class AuthView {
    /**
     * Auth views base path
     * @var string
     */
    protected $viewsPath;

    /**
     * Constructor
     */
    public function __construct() {
        $this->viewsPath = root_path('app/Views/auth');
    }

    /**
     * Render an auth view with layout
     *
     * @param string $template Template name (e.g., 'login')
     * @param array $data Data to pass to view
     * @return string
     */
    public function render($template, array $data = []) {
        // Render main content
        $content = $this->renderPartial($template, $data);

        // Render layout with content
        $layoutPath = root_path('app/Views/layouts/auth.php');

        if (!file_exists($layoutPath)) {
            throw new \Exception("Auth layout not found at {$layoutPath}");
        }

        $data['content'] = $content;
        return $this->renderFile($layoutPath, $data);
    }

    /**
     * Render partial template (without layout)
     *
     * @param string $template
     * @param array $data
     * @return string
     */
    public function renderPartial($template, array $data = []) {
        // Convert dot notation to path (if needed)
        $template = str_replace('.', '/', $template);

        // Full path
        $path = $this->viewsPath . "/{$template}.php";

        if (!file_exists($path)) {
            throw new \Exception("Auth template '{$template}' not found at {$path}");
        }

        return $this->renderFile($path, $data);
    }

    /**
     * Render a file with data
     *
     * @param string $path
     * @param array $data
     * @return string
     */
    protected function renderFile($path, array $data = []) {
        // Extract data for convenience
        extract($data, EXTR_SKIP);

        // Start output buffering
        ob_start();

        // Include template file
        include $path;

        // Return buffered content
        return ob_get_clean();
    }
}
