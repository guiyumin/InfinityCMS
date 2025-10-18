<?php

namespace App\Core;

/**
 * Application Service Container
 * 应用服务容器（单例模式）
 *
 * 负责管理和提供核心服务实例
 */
class App {
    /**
     * Singleton instance
     * @var App
     */
    protected static $instance;

    /**
     * Registered services
     * @var array
     */
    protected $services = [];

    /**
     * Private constructor (singleton)
     */
    private function __construct() {}

    /**
     * Get singleton instance
     * 获取单例实例
     *
     * @return App
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Bind a service to container
     * 绑定服务到容器
     *
     * @param string $name Service name
     * @param mixed $service Service instance or callable
     * @return void
     */
    public function bind($name, $service) {
        $this->services[$name] = $service;
    }

    /**
     * Get a service from container
     * 从容器获取服务
     *
     * @param string $name Service name
     * @return mixed|null
     */
    public function get($name) {
        if (!isset($this->services[$name])) {
            return null;
        }

        $service = $this->services[$name];

        // If service is a callable, execute it
        if (is_callable($service)) {
            $this->services[$name] = $service($this);
            return $this->services[$name];
        }

        return $service;
    }

    /**
     * Check if service exists
     * 检查服务是否存在
     *
     * @param string $name
     * @return bool
     */
    public function has($name) {
        return isset($this->services[$name]);
    }

    /**
     * Magic method to get service
     * 魔术方法快捷访问服务
     *
     * @param string $name
     * @return mixed|null
     */
    public function __get($name) {
        return $this->get($name);
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Prevent unserialization
     */
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}
