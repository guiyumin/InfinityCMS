<?php

namespace App\Http\Middlewares;

use App\Core\Request;

/**
 * Authentication Middleware
 * 认证中间件
 */
class AuthMiddleware {
    /**
     * Handle request
     *
     * @param Request $request
     * @return bool
     */
    public function handle(Request $request) {
        // Check if user is logged in
        if (!is_logged_in()) {
            // Redirect to login page
            redirect(url('/login'));
            return false;
        }

        return true;
    }
}
