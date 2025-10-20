<?php

namespace App\Core;

/**
 * Setup View
 *
 * Simple view renderer for setup wizard.
 * Setup is core infrastructure and should NOT use themes.
 */
class SetupView {
    /**
     * Setup views base path
     * @var string
     */
    protected $viewsPath;

    /**
     * Constructor
     */
    public function __construct() {
        $this->viewsPath = root_path('app/Views/setup');
    }

    /**
     * Render a setup view with layout
     *
     * @param string $template Template name (e.g., 'index')
     * @param array $data Data to pass to view
     * @return string
     */
    public function render($template, array $data = []) {
        // Render main content
        $content = $this->renderPartial($template, $data);

        // Render layout with content
        $layoutPath = root_path('app/Views/layouts/setup.php');

        if (!file_exists($layoutPath)) {
            throw new \Exception("Setup layout not found at {$layoutPath}");
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
            throw new \Exception("Setup template '{$template}' not found at {$path}");
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
