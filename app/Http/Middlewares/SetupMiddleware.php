<?php

namespace App\Http\Middlewares;

use App\Core\Request;
use App\Core\Migration;

/**
 * Setup Middleware
 *
 * Detects if the CMS needs first-time setup and redirects to setup wizard.
 * Checks if database is initialized and migrations are run.
 */
class SetupMiddleware {
    /**
     * Handle the middleware
     *
     * @param Request $request
     * @return bool
     */
    public function handle(Request $request) {
        $isSetupRoute = $this->isSetupRoute($request->uri());
        $needsSetup = $this->needsSetup();

        // If on setup page but setup is already complete, redirect to home
        if ($isSetupRoute && !$needsSetup) {
            redirect(url('/'));
            return false;
        }

        // If not on setup page and setup is needed, redirect to setup
        if (!$isSetupRoute && $needsSetup) {
            redirect(url('/setup'));
            return false;
        }

        return true;
    }

    /**
     * Check if current route is setup-related
     *
     * @param string $uri
     * @return bool
     */
    protected function isSetupRoute($uri) {
        return str_starts_with($uri, '/setup');
    }

    /**
     * Check if CMS needs setup
     *
     * @return bool
     */
    protected function needsSetup() {
        // First check if config.php exists
        if (defined('CONFIG_MISSING') && CONFIG_MISSING === true) {
            return true; // No config file, definitely needs setup
        }

        // Check if database connection failed
        $app = \App\Core\App::getInstance();
        if ($app->has('db_connection_error')) {
            // Database connection failed, could be wrong credentials
            // Only redirect to setup if config is missing
            // Otherwise show error page
            if (CONFIG_MISSING) {
                return true;
            }
            // Config exists but database connection failed - this is an error
            // Don't redirect to setup, let the error handler deal with it
            return false;
        }

        // Check if already setup (via session flag)
        if (isset($_SESSION['_cms_setup_complete'])) {
            return false;
        }

        try {
            // Check if users table exists and has at least one user
            $db = db();

            // Check if db actually has a connection
            if (!$db->getPdo()) {
                return CONFIG_MISSING; // Only need setup if config is missing
            }

            $users = $db->query("SELECT COUNT(*) as count FROM users");

            if ($users && $users[0]['count'] > 0) {
                // Setup complete, mark in session
                $_SESSION['_cms_setup_complete'] = true;
                return false;
            }

            // No users found, setup needed
            return true;
        } catch (\Exception $e) {
            // Database error - likely tables don't exist
            // Only redirect to setup if this is initial setup
            return CONFIG_MISSING;
        }
    }

    /**
     * Clear setup flag (useful for re-running setup)
     *
     * @return void
     */
    public static function clearSetupFlag() {
        unset($_SESSION['_cms_setup_complete']);
    }
}
