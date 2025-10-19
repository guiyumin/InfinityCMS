<?php

namespace App\Core;

/**
 * HTTP Request Handler
 * HTTP 请求处理类
 */
class Request {
    /**
     * Request method
     * @var string
     */
    protected $method;

    /**
     * Request URI
     * @var string
     */
    protected $uri;

    /**
     * Request parameters
     * @var array
     */
    protected $params = [];

    /**
     * Constructor
     */
    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = $this->parseUri();
        $this->params = $this->parseParams();
    }

    /**
     * Parse request URI
     * 解析请求 URI
     *
     * @return string
     */
    protected function parseUri() {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        // Remove query string
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }

        // Remove /public/ prefix if present (for shared hosting setups)
        // This handles cases where .htaccess rewrites requests to /public/
        $uri = preg_replace('#^/public/#', '/', $uri);

        return '/' . trim($uri, '/');
    }

    /**
     * Parse request parameters
     * 解析请求参数
     *
     * @return array
     */
    protected function parseParams() {
        $params = [];

        // GET parameters
        $params = array_merge($params, $_GET);

        // POST parameters
        if ($this->method === 'POST') {
            $params = array_merge($params, $_POST);
        }

        // JSON body
        if ($this->isJson()) {
            $json = json_decode(file_get_contents('php://input'), true);
            if (is_array($json)) {
                $params = array_merge($params, $json);
            }
        }

        return $params;
    }

    /**
     * Get request method
     * 获取请求方法
     *
     * @return string
     */
    public function method() {
        return $this->method;
    }

    /**
     * Get request URI
     * 获取请求 URI
     *
     * @return string
     */
    public function uri() {
        return $this->uri;
    }

    /**
     * Get request parameter
     * 获取请求参数
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function input($key = null, $default = null) {
        if ($key === null) {
            return $this->params;
        }
        return $this->params[$key] ?? $default;
    }

    /**
     * Check if parameter exists
     * 检查参数是否存在
     *
     * @param string $key
     * @return bool
     */
    public function has($key) {
        return isset($this->params[$key]);
    }

    /**
     * Get all parameters
     * 获取所有参数
     *
     * @return array
     */
    public function all() {
        return $this->params;
    }

    /**
     * Get GET parameter
     * 获取 GET 参数
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    /**
     * Get POST parameter
     * 获取 POST 参数
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function post($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }

    /**
     * Check if request is GET
     * 检查是否为 GET 请求
     *
     * @return bool
     */
    public function isGet() {
        return $this->method === 'GET';
    }

    /**
     * Check if request is POST
     * 检查是否为 POST 请求
     *
     * @return bool
     */
    public function isPost() {
        return $this->method === 'POST';
    }

    /**
     * Check if request is AJAX
     * 检查是否为 AJAX 请求
     *
     * @return bool
     */
    public function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Check if request is JSON
     * 检查是否为 JSON 请求
     *
     * @return bool
     */
    public function isJson() {
        return isset($_SERVER['CONTENT_TYPE']) &&
               strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false;
    }

    /**
     * Check if request is HTMX
     * 检查是否为 HTMX 请求
     *
     * @return bool
     */
    public function isHtmx() {
        return isset($_SERVER['HTTP_HX_REQUEST']);
    }

    /**
     * Get uploaded file
     * 获取上传文件
     *
     * @param string $key
     * @return array|null
     */
    public function file($key) {
        return $_FILES[$key] ?? null;
    }

    /**
     * Get header value
     * 获取请求头
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function header($key, $default = null) {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $_SERVER[$key] ?? $default;
    }

    /**
     * Validate CSRF token
     * 验证 CSRF Token
     *
     * @return bool
     */
    public function validateCsrf() {
        $token = $this->input('_csrf_token');
        $sessionToken = $_SESSION['_csrf_token'] ?? '';
        return $token && hash_equals($sessionToken, $token);
    }
}
