<?php

namespace App\Core;

/**
 * Router - Route matching and dispatching
 * 路由器 - 路由匹配和调度
 */
class Router {
    /**
     * Registered routes
     * @var array
     */
    protected $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
        'PATCH' => [],
    ];

    /**
     * Global middleware stack (runs on every request)
     * @var array
     */
    protected $globalMiddleware = [];

    /**
     * Middleware stack
     * @var array
     */
    protected $middleware = [];

    /**
     * Route group prefix
     * @var string
     */
    protected $groupPrefix = '';

    /**
     * Route group middleware
     * @var array
     */
    protected $groupMiddleware = [];

    /**
     * Register GET route
     * 注册 GET 路由
     *
     * @param string $uri
     * @param mixed $action
     * @return void
     */
    public function get($uri, $action) {
        $this->addRoute('GET', $uri, $action);
    }

    /**
     * Register POST route
     * 注册 POST 路由
     *
     * @param string $uri
     * @param mixed $action
     * @return void
     */
    public function post($uri, $action) {
        $this->addRoute('POST', $uri, $action);
    }

    /**
     * Register PUT route
     * 注册 PUT 路由
     *
     * @param string $uri
     * @param mixed $action
     * @return void
     */
    public function put($uri, $action) {
        $this->addRoute('PUT', $uri, $action);
    }

    /**
     * Register DELETE route
     * 注册 DELETE 路由
     *
     * @param string $uri
     * @param mixed $action
     * @return void
     */
    public function delete($uri, $action) {
        $this->addRoute('DELETE', $uri, $action);
    }

    /**
     * Register PATCH route
     * 注册 PATCH 路由
     *
     * @param string $uri
     * @param mixed $action
     * @return void
     */
    public function patch($uri, $action) {
        $this->addRoute('PATCH', $uri, $action);
    }

    /**
     * Add route to collection
     * 添加路由到集合
     *
     * @param string $method
     * @param string $uri
     * @param mixed $action
     * @return void
     */
    protected function addRoute($method, $uri, $action) {
        $uri = $this->groupPrefix . '/' . trim($uri, '/');
        $uri = '/' . trim($uri, '/');

        $this->routes[$method][$uri] = [
            'action' => $action,
            'middleware' => $this->groupMiddleware,
        ];
    }

    /**
     * Define a route group
     * 定义路由组
     *
     * @param array $attributes
     * @param callable $callback
     * @return void
     */
    public function group(array $attributes, callable $callback) {
        $previousPrefix = $this->groupPrefix;
        $previousMiddleware = $this->groupMiddleware;

        // Apply group prefix
        if (isset($attributes['prefix'])) {
            $this->groupPrefix = $previousPrefix . '/' . trim($attributes['prefix'], '/');
        }

        // Apply group middleware
        if (isset($attributes['middleware'])) {
            $middleware = is_array($attributes['middleware'])
                ? $attributes['middleware']
                : [$attributes['middleware']];
            $this->groupMiddleware = array_merge($previousMiddleware, $middleware);
        }

        // Execute callback
        $callback($this);

        // Restore previous values
        $this->groupPrefix = $previousPrefix;
        $this->groupMiddleware = $previousMiddleware;
    }

    /**
     * Register global middleware
     * 注册全局中间件
     *
     * @param string $middlewareName
     * @return void
     */
    public function addGlobalMiddleware($middlewareName) {
        $this->globalMiddleware[] = $middlewareName;
    }

    /**
     * Dispatch current request
     * 调度当前请求
     *
     * @return void
     */
    public function dispatch() {
        /** @var Request $request */
        $request = app('request');
        /** @var Response $response */
        $response = app('response');
        $method = $request->method();
        $uri = $request->uri();

        // Run global middleware first
        if (!empty($this->globalMiddleware)) {
            foreach ($this->globalMiddleware as $middlewareName) {
                if (!$this->runMiddleware($middlewareName, $request)) {
                    return; // Global middleware blocked the request
                }
            }
        }

        // Find matching route
        $route = $this->findRoute($method, $uri);

        if ($route === null) {
            $response->notFound();
            return;
        }

        // Run route-specific middleware
        if (!empty($route['middleware'])) {
            foreach ($route['middleware'] as $middlewareName) {
                if (!$this->runMiddleware($middlewareName, $request)) {
                    return; // Middleware blocked the request
                }
            }
        }

        // Execute route action
        $output = $this->executeAction($route['action'], $route['params'] ?? []);

        // Send response
        if (is_string($output)) {
            $response->html($output);
        }
    }

    /**
     * Find matching route
     * 查找匹配的路由
     *
     * @param string $method
     * @param string $uri
     * @return array|null
     */
    protected function findRoute($method, $uri) {
        if (!isset($this->routes[$method])) {
            return null;
        }

        foreach ($this->routes[$method] as $routeUri => $route) {
            $pattern = $this->convertToRegex($routeUri);

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove full match
                return [
                    'action' => $route['action'],
                    'middleware' => $route['middleware'],
                    'params' => $matches,
                ];
            }
        }

        return null;
    }

    /**
     * Convert route URI to regex pattern
     * 将路由 URI 转换为正则表达式
     *
     * @param string $uri
     * @return string
     */
    protected function convertToRegex($uri) {
        // Replace {param} with named capture group
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $uri);
        return '#^' . $pattern . '$#';
    }

    /**
     * Execute route action
     * 执行路由动作
     *
     * @param mixed $action
     * @param array $params
     * @return mixed
     */
    protected function executeAction($action, array $params = []) {
        // If action is a closure
        if ($action instanceof \Closure) {
            return call_user_func_array($action, $params);
        }

        // If action is Controller@method string
        if (is_string($action)) {
            return $this->executeControllerAction($action, $params);
        }

        return null;
    }

    /**
     * Execute controller action
     * 执行控制器方法
     *
     * @param string $action
     * @param array $params
     * @return mixed
     */
    protected function executeControllerAction($action, array $params = []) {
        list($controller, $method) = explode('@', $action);

        // Convert forward slashes to backslashes for namespaces
        $controller = str_replace('/', '\\', $controller);

        // Support both full namespace and short name
        // Check if it starts with App\ to determine if it's already fully qualified
        if (strpos($controller, 'App\\') !== 0) {
            $controller = 'App\\Http\\Controllers\\' . $controller;
        }

        if (!class_exists($controller)) {
            throw new \Exception("Controller {$controller} not found");
        }

        $instance = new $controller();

        if (!method_exists($instance, $method)) {
            throw new \Exception("Method {$method} not found in {$controller}");
        }

        return call_user_func_array([$instance, $method], $params);
    }

    /**
     * Run middleware
     * 执行中间件
     *
     * @param string $middlewareName
     * @param Request $request
     * @return bool
     */
    protected function runMiddleware($middlewareName, $request) {
        $middlewareClass = 'App\\Http\\Middlewares\\' . ucfirst($middlewareName) . 'Middleware';

        if (!class_exists($middlewareClass)) {
            // Try without 'Middleware' suffix
            $middlewareClass = 'App\\Http\\Middlewares\\' . ucfirst($middlewareName);
        }

        if (!class_exists($middlewareClass)) {
            throw new \Exception("Middleware {$middlewareName} not found");
        }

        $middleware = new $middlewareClass();
        return $middleware->handle($request);
    }

    /**
     * Get all registered routes
     * 获取所有注册的路由
     *
     * @return array
     */
    public function getRoutes() {
        return $this->routes;
    }
}
