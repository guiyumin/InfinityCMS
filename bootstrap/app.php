<?php
/**
 * Application Bootstrap
 * 应用启动文件
 *
 * 此文件负责初始化整个应用程序
 */

// 1. 加载 PSR-4 Autoloader
require __DIR__ . '/../autoloader.php';

$loader = new Psr4Autoloader();
$loader->addNamespace('App', __DIR__ . '/../app');
$loader->register();

// 2. 加载全局辅助函数
require __DIR__ . '/../functions.php';

// 3. 加载环境配置
$env = require __DIR__ . '/../.env.php';

// 4. 设置错误报告
if ($env['app']['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// 4.5 设置全局异常处理器
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

// 5. 设置时区
date_default_timezone_set($env['app']['timezone'] ?? 'UTC');

// 6. 启动 Session
session_start([
    'name' => $env['session']['name'],
    'cookie_lifetime' => $env['session']['lifetime'] * 60,
    'cookie_path' => $env['session']['path'],
    'cookie_secure' => $env['session']['secure'],
    'cookie_httponly' => $env['session']['httponly'],
]);

// 7. 初始化核心服务容器
$app = App\Core\App::getInstance();

// 绑定配置服务
$app->bind('config', new App\Core\Config($env));

// 绑定请求和响应
$app->bind('request', new App\Core\Request());
$app->bind('response', new App\Core\Response());

// 绑定数据库
$app->bind('db', new App\Core\DB($env['database']));

// 绑定路由
$app->bind('router', new App\Core\Router());

// 绑定钩子系统
$app->bind('hook', new App\Core\Hook());

// 绑定视图
$app->bind('view', new App\Core\View($env['app']['theme']));

// 绑定管理后台视图
$app->bind('admin_view', new App\Core\AdminView());

// 8. 加载插件
$pluginsPath = __DIR__ . '/../plugins';
if (is_dir($pluginsPath)) {
    foreach (glob($pluginsPath . '/*/plugin.php') as $pluginFile) {
        $pluginDir = dirname($pluginFile);
        $pluginName = basename($pluginDir);

        // 为插件注册命名空间
        $loader->addNamespace(
            'Plugins\\' . str_replace('-', '', ucwords($pluginName, '-')),
            $pluginDir
        );

        // 加载插件入口文件
        require $pluginFile;
    }
}

// 9. 注册路由
require __DIR__ . '/../config/routes.php';

// 10. 返回应用实例
return $app;
