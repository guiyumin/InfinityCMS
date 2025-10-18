<?php

namespace App\Core;

/**
 * HTTP Response Handler
 * HTTP 响应处理类
 */
class Response {
    /**
     * Response content
     * @var string
     */
    protected $content = '';

    /**
     * HTTP status code
     * @var int
     */
    protected $statusCode = 200;

    /**
     * Response headers
     * @var array
     */
    protected $headers = [];

    /**
     * Set response content
     * 设置响应内容
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    /**
     * Set HTTP status code
     * 设置 HTTP 状态码
     *
     * @param int $code
     * @return $this
     */
    public function setStatusCode($code) {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Set response header
     * 设置响应头
     *
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function setHeader($key, $value) {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Send JSON response
     * 发送 JSON 响应
     *
     * @param mixed $data
     * @param int $statusCode
     * @return void
     */
    public function json($data, $statusCode = 200) {
        $this->setStatusCode($statusCode);
        $this->setHeader('Content-Type', 'application/json');
        $this->setContent(json_encode($data, JSON_UNESCAPED_UNICODE));
        $this->send();
    }

    /**
     * Send HTML response
     * 发送 HTML 响应
     *
     * @param string $html
     * @param int $statusCode
     * @return void
     */
    public function html($html, $statusCode = 200) {
        $this->setStatusCode($statusCode);
        $this->setHeader('Content-Type', 'text/html; charset=UTF-8');
        $this->setContent($html);
        $this->send();
    }

    /**
     * Redirect to URL
     * 重定向
     *
     * @param string $url
     * @param int $statusCode
     * @return void
     */
    public function redirect($url, $statusCode = 302) {
        $this->setStatusCode($statusCode);
        $this->setHeader('Location', $url);
        $this->send();
    }

    /**
     * Send 404 Not Found response
     * 发送 404 响应
     *
     * @param string $message
     * @return void
     */
    public function notFound($message = 'Page Not Found') {
        $this->setStatusCode(404);

        // Try to render error template
        try {
            if (app('view')->exists('errors.404')) {
                $content = app('view')->render('errors.404', [
                    'title' => '404 Not Found',
                    'message' => $message,
                ], null); // No layout
                $this->setContent($content);
            } else {
                $this->setContent($message);
            }
        } catch (\Exception $e) {
            // Fallback to plain text if template fails
            $this->setContent($message);
        }

        $this->send();
    }

    /**
     * Send 403 Forbidden response
     * 发送 403 响应
     *
     * @param string $message
     * @return void
     */
    public function forbidden($message = 'Forbidden') {
        $this->setStatusCode(403);
        $this->setContent($message);
        $this->send();
    }

    /**
     * Send 500 Internal Server Error response
     * 发送 500 响应
     *
     * @param string $message
     * @param string $details Additional error details (shown in debug mode)
     * @return void
     */
    public function error($message = 'Internal Server Error', $details = '') {
        $this->setStatusCode(500);

        // Try to render error template
        try {
            if (app('view')->exists('errors.500')) {
                $content = app('view')->render('errors.500', [
                    'title' => '500 Internal Server Error',
                    'message' => $message,
                    'error_details' => $details,
                ], null); // No layout
                $this->setContent($content);
            } else {
                $this->setContent($message);
            }
        } catch (\Exception $e) {
            // Fallback to plain text if template fails
            $this->setContent($message . ($details ? "\n\n" . $details : ''));
        }

        $this->send();
    }

    /**
     * Send the response
     * 发送响应
     *
     * @return void
     */
    public function send() {
        // Set status code
        http_response_code($this->statusCode);

        // Send headers
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        // Send content
        echo $this->content;

        // Exit
        exit;
    }

    /**
     * Download file
     * 下载文件
     *
     * @param string $filepath
     * @param string|null $filename
     * @return void
     */
    public function download($filepath, $filename = null) {
        if (!file_exists($filepath)) {
            $this->notFound('File not found');
            return;
        }

        $filename = $filename ?? basename($filepath);

        $this->setHeader('Content-Type', 'application/octet-stream');
        $this->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $this->setHeader('Content-Length', filesize($filepath));
        $this->setContent(file_get_contents($filepath));
        $this->send();
    }
}
