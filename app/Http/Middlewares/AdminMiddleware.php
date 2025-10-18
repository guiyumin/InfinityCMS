<?php

namespace App\Http\Middlewares;

use App\Core\Request;
use App\Core\Migration;

/**
 * Admin Middleware
 * Handles admin authentication and checks for pending migrations
 */
class AdminMiddleware {
    /**
     * Session cache duration in seconds (5 minutes)
     */
    const CACHE_TTL = 300;

    /**
     * Handle the middleware
     *
     * @param Request $request
     * @return bool
     */
    public function handle(Request $request) {
        // Check if user is logged in
        if (!is_logged_in()) {
            redirect(url('/login'));
            return false;
        }

        // Check for pending migrations with session caching
        $this->checkPendingMigrations();

        return true;
    }

    /**
     * Check for pending migrations and share with views
     * Uses session caching to minimize performance impact
     *
     * @return void
     */
    protected function checkPendingMigrations() {
        $now = time();

        // Check if we need to refresh the cache
        $needsCheck = !isset($_SESSION['_migrations_checked_at']) ||
                      ($now - $_SESSION['_migrations_checked_at']) > self::CACHE_TTL;

        if ($needsCheck) {
            try {
                $migration = new Migration();
                $hasPending = $migration->hasPendingMigrations();

                // Cache the result in session
                $_SESSION['_has_pending_migrations'] = $hasPending;
                $_SESSION['_migrations_checked_at'] = $now;

                // If there are pending migrations, get the count
                if ($hasPending) {
                    $pending = $migration->getPendingMigrations();
                    $_SESSION['_pending_migrations_count'] = count($pending);
                }
            } catch (\Exception $e) {
                // Silently fail - don't break admin access if migration check fails
                $_SESSION['_has_pending_migrations'] = false;
                $_SESSION['_migrations_checked_at'] = $now;
            }
        }

        // Share data with all views
        $hasPending = $_SESSION['_has_pending_migrations'] ?? false;
        $count = $_SESSION['_pending_migrations_count'] ?? 0;

        app('view')->share([
            'hasPendingMigrations' => $hasPending,
            'pendingMigrationsCount' => $count,
        ]);
    }

    /**
     * Clear the migrations cache
     * Useful after running migrations
     *
     * @return void
     */
    public static function clearMigrationsCache() {
        unset($_SESSION['_has_pending_migrations']);
        unset($_SESSION['_pending_migrations_count']);
        unset($_SESSION['_migrations_checked_at']);
    }
}
