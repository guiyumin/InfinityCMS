<?php
/**
 * Application Bootstrap
 *
 * This file is responsible for initializing the entire application
 */

// 1. Load PSR-4 Autoloader
require __DIR__ . '/../autoloader.php';

$loader = new Psr4Autoloader();
$loader->addNamespace('App', __DIR__ . '/../app');
$loader->register();

// 2. Load global helper functions
require __DIR__ . '/../functions.php';

// 3. Load environment configuration
$env = require __DIR__ . '/../.env.php';

// 4. Set error reporting
if ($env['app']['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// 4.5 Set global exception handler
set_exception_handler(function($exception) use ($env) {
    // Log the error
    error_log($exception->getMessage() . ' in ' . $exception->getFile() . ':' . $exception->getLine());

    // Get details
    $message = $exception->getMessage();
    $details = sprintf(
        "Error: %s\nFile: %s\nLine: %d\nTrace:\n%s",
        $message,
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTraceAsString()
    );

    // Try to use Response class if available
    try {
        if (class_exists('App\Core\App')) {
            $app = App\Core\App::getInstance();
            if ($app->has('response')) {
                $response = $app->get('response');
                $response->error('Internal Server Error', $details);
                return;
            }
        }
    } catch (\Exception $e) {
        // Response not available, fallback below
    }

    // Fallback error display
    http_response_code(500);
    if ($env['app']['debug']) {
        echo "<h1>Internal Server Error</h1>";
        echo "<pre>" . htmlspecialchars($details) . "</pre>";
    } else {
        echo "<h1>Internal Server Error</h1>";
        echo "<p>Something went wrong. Please try again later.</p>";
    }
    exit;
});

// 5. Set timezone
date_default_timezone_set($env['app']['timezone'] ?? 'UTC');

// 6. Start Session
session_start([
    'name' => $env['session']['name'],
    'cookie_lifetime' => $env['session']['lifetime'] * 60,
    'cookie_path' => $env['session']['path'],
    'cookie_secure' => $env['session']['secure'],
    'cookie_httponly' => $env['session']['httponly'],
]);

// 7. Initialize core service container
$app = App\Core\App::getInstance();

// Bind configuration service
$app->bind('config', new App\Core\Config($env));

// Bind request and response
$app->bind('request', new App\Core\Request());
$app->bind('response', new App\Core\Response());

// Bind database - try to connect, but allow setup to handle failures
try {
    $app->bind('db', new App\Core\DB($env['database']));
} catch (\Exception $e) {
    // Database connection failed - this might be initial setup
    // Create a placeholder DB instance without connection for setup process
    $db = new App\Core\DB(); // Empty constructor won't connect
    $app->bind('db', $db);

    // Store the error for setup controller to handle
    $app->bind('db_connection_error', $e->getMessage());
}

// Bind router
$app->bind('router', new App\Core\Router());

// Bind hook system
$app->bind('hook', new App\Core\Hook());

// Bind view
$app->bind('view', new App\Core\View($env['app']['theme']));

// Bind admin view
$app->bind('admin_view', new App\Core\AdminView());

// Bind setup view
$app->bind('setup_view', new App\Core\SetupView());

// Bind auth view
$app->bind('auth_view', new App\Core\AuthView());

// 8. Load plugins
$pluginsPath = __DIR__ . '/../plugins';
if (is_dir($pluginsPath)) {
    foreach (glob($pluginsPath . '/*/plugin.php') as $pluginFile) {
        $pluginDir = dirname($pluginFile);
        $pluginName = basename($pluginDir);

        // Register namespace for plugin
        $loader->addNamespace(
            'Plugins\\' . str_replace('-', '', ucwords($pluginName, '-')),
            $pluginDir
        );

        // Load plugin entry file
        require $pluginFile;
    }
}

// 9. Register global middleware
// Setup middleware redirects to setup wizard if app is not configured
$router = $app->get('router');
$router->addGlobalMiddleware('Setup');

// 10. Register routes
require __DIR__ . '/../config/routes.php';

// 11. Return app instance
return $app;
