<?php

namespace App\Core;

/**
 * Configuration Manager
 * 配置管理器
 */
class Config {
    /**
     * Configuration data from config.php file
     * @var array
     */
    protected $file_configs = [];

    /**
     * Configuration data from database settings table
     * @var array
     */
    protected $db_configs = [];

    /**
     * Constructor
     * Automatically initializes configuration from file and database
     */
    public function __construct() {
        $this->init();
    }

    /**
     * Initialize configuration
     * Reads root config.php and loads all settings from database
     *
     * @return void
     */
    public function init() {
        // Load file configs
        $configPath = root_path() . '/config.php';

        if (file_exists($configPath)) {
            // Include the config file and get the returned array
            $loadedConfig = include $configPath;
            $this->file_configs = $loadedConfig;
        } else {
            $this->file_configs = [];
        }

        // Load database settings
        try {
            $app = App::getInstance();
            if (!$app->has('db')) {
                $this->db_configs = [];
                return;
            }

             $db = $app->get('db');

            // Check if settings table exists
            $tables = $db->query("SHOW TABLES LIKE 'settings'");

            if (empty($tables)) {
                $this->db_configs = [];
                return;    
            }

            $results = $db->query("SELECT setting_key, setting_value FROM settings");

            foreach ($results as $row) {
                $this->db_configs[$row['setting_key']] = $row['setting_value'];
            }
        } catch (\Exception $e) {
            // Database not available or settings table doesn't exist yet
            // This is normal during setup or if database is not configured
            $this->db_configs = [];
        }
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
        $value = array_merge($this->file_configs, $this->db_configs);

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
        $config = &$this->file_configs;

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
        $value = array_merge($this->file_configs, $this->db_configs);

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
        return array_merge($this->file_configs, $this->db_configs);
    }

    /**
     * Get a setting from database
     * 从数据库获取设置
     *
     * @param string $key Setting key
     * @param mixed $default Default value if not found
     * @return mixed
     */
    public function getSetting($key, $default = null) {
        return $this->db_configs[$key] ?? $default;
    }

    /**
     * Set a setting in database
     * 保存设置到数据库
     *
     * @param string $key Setting key
     * @param mixed $value Setting value
     * @param string|null $description Optional description
     * @return bool
     */
    public function setSetting($key, $value, $description = null) {
        try {
            $app = App::getInstance();
            if (!$app->has('db')) {
                return false;
            }

            $db = $app->get('db');

            // Check if setting exists
            $existing = $db->query(
                "SELECT id FROM settings WHERE setting_key = ?",
                [$key]
            );

            if (!empty($existing)) {
                // Update existing
                $db->query(
                    "UPDATE settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?",
                    [$value, $key]
                );
            } else {
                // Insert new
                $db->query(
                    "INSERT INTO settings (setting_key, setting_value, description) VALUES (?, ?, ?)",
                    [$key, $value, $description]
                );
            }

            // Update cache
            $this->db_configs[$key] = $value;

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get all database settings
     * 获取所有数据库设置
     *
     * @return array
     */
    public function getAllSettings() {
        return $this->db_configs;
    }

    /**
     * Delete a setting from database
     * 从数据库删除设置
     *
     * @param string $key Setting key
     * @return bool
     */
    public function deleteSetting($key) {
        try {
            $app = App::getInstance();
            if (!$app->has('db')) {
                return false;
            }

            $db = $app->get('db');
            $db->query("DELETE FROM settings WHERE setting_key = ?", [$key]);

            // Update cache
            if (isset($this->db_configs[$key])) {
                unset($this->db_configs[$key]);
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
