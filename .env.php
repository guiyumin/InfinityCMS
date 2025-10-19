<?php
/**
 * Environment Configuration
 * 环境配置文件
 *
 * 注意：此文件包含敏感信息，不应提交到版本控制
 */

return [
    // 应用配置
    'app' => [
        'name' => 'Infinity CMS',
        'url' => '',
        'debug' => true,
        'theme' => 'infinity',
        'timezone' => 'America/Los_Angeles',
    ],

    // 数据库配置
    'database' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'infinity_cms',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],

    // Session 配置
    'session' => [
        'lifetime' => 120, // minutes
        'name' => 'infinity_cms_session',
        'path' => '/',
        'secure' => false, // 生产环境建议设为 true（需要 HTTPS）
        'httponly' => true,
    ],

    // 安全配置
    'security' => [
        'csrf_enabled' => true,
    ],

    // 文件上传配置
    'upload' => [
        'max_size' => 5 * 1024 * 1024, // 5MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'],
        'path' => __DIR__ . '/storage/uploads',
    ],
];
