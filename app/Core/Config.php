<?php

namespace App\Core;

/**
 * Configuration Manager
 * 配置管理器
 */
class Config {
    /**
     * Configuration data
     * @var array
     */
    protected $config = [];

    /**
     * Constructor
     *
     * @param array $config Configuration array from config.php
     */
    public function __construct(array $config = []) {
        $this->config = $config;
    }

    /**
     * Get configuration value using dot notation
     * 使用点号访问配置值
     *
     * Examples:
     * - config('app.name')
     * - config('database.driver')
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null) {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * Set configuration value using dot notation
     * 设置配置值
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value) {
        $keys = explode('.', $key);
        $config = &$this->config;

        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!isset($config[$key]) || !is_array($config[$key])) {
                $config[$key] = [];
            }
            $config = &$config[$key];
        }

        $config[array_shift($keys)] = $value;
    }

    /**
     * Check if configuration key exists
     * 检查配置键是否存在
     *
     * @param string $key
     * @return bool
     */
    public function has($key) {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return false;
            }
            $value = $value[$segment];
        }

        return true;
    }

    /**
     * Get all configuration
     * 获取所有配置
     *
     * @return array
     */
    public function all() {
        return $this->config;
    }
}
