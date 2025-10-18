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
        // Skip setup check if already on setup page
        if ($this->isSetupRoute($request->uri())) {
            return true;
        }

        // Check if setup is needed
        if ($this->needsSetup()) {
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
        // Check if already setup (via session flag)
        if (isset($_SESSION['_cms_setup_complete'])) {
            return false;
        }

        try {
            // Check if users table exists and has at least one user
            $db = db();
            $users = $db->query("SELECT COUNT(*) as count FROM users");

            if ($users && $users[0]['count'] > 0) {
                // Setup complete, mark in session
                $_SESSION['_cms_setup_complete'] = true;
                return false;
            }

            // No users found, setup needed
            return true;
        } catch (\Exception $e) {
            // Database error - likely tables don't exist, setup needed
            return true;
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
