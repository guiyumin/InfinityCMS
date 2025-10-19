# Configuration Guide

This guide covers all configuration options available in Infinity CMS.

## Table of Contents
- [Main Configuration](#main-configuration)
- [Database Configuration](#database-configuration)
- [Application Settings](#application-settings)
- [Security Settings](#security-settings)
- [Email Configuration](#email-configuration)
- [Cache Configuration](#cache-configuration)
- [File Upload Settings](#file-upload-settings)
- [Advanced Configuration](#advanced-configuration)
- [Environment Variables](#environment-variables)

## Main Configuration

The main configuration file is `config.php` in the root directory. Never commit this file to version control.

### Basic Structure
```php
<?php
return [
    'database' => [...],
    'app' => [...],
    'security' => [...],
    'mail' => [...],
    'cache' => [...],
    'uploads' => [...],
];
```

## Database Configuration

### MySQL/MariaDB
```php
'database' => [
    'driver' => 'mysql',
    'host' => 'localhost',
    'port' => 3306,
    'name' => 'infinity_cms',
    'user' => 'db_user',
    'pass' => 'db_password',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => 'inf_',
    'strict' => true,
    'engine' => 'InnoDB',
    'options' => [
        PDO::ATTR_PERSISTENT => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]
],
```

### Connection Pooling
```php
'database' => [
    // ... other settings
    'pool' => [
        'min' => 2,
        'max' => 10,
        'idle_timeout' => 60, // seconds
    ]
],
```

### Read/Write Splitting
```php
'database' => [
    'read' => [
        'host' => ['192.168.1.1', '192.168.1.2'],
    ],
    'write' => [
        'host' => '192.168.1.3',
    ],
    // ... other settings
],
```

## Application Settings

### Basic Settings
```php
'app' => [
    'name' => 'My Website',
    'tagline' => 'Your tagline here',
    'url' => 'https://yourdomain.com',
    'admin_url' => 'https://yourdomain.com/admin',
    'env' => 'production', // production, staging, development
    'debug' => false,
    'timezone' => 'UTC',
    'locale' => 'en_US',
    'charset' => 'UTF-8',
],
```

### Theme and Appearance
```php
'app' => [
    // ... other settings
    'theme' => 'infinity',
    'admin_theme' => 'default',
    'mobile_theme' => 'auto', // auto, specific theme, or false
    'theme_cache' => true,
],
```

### Content Settings
```php
'app' => [
    // ... other settings
    'posts_per_page' => 10,
    'excerpt_length' => 200,
    'comment_registration' => false,
    'comment_moderation' => true,
    'pingback_enabled' => true,
    'markdown_enabled' => true,
    'editor' => 'markdown', // markdown, html, blocks
],
```

### URL Settings
```php
'app' => [
    // ... other settings
    'pretty_urls' => true,
    'trailing_slash' => false,
    'force_https' => true,
    'www_redirect' => 'non-www', // www, non-www, or false
],
```

## Security Settings

### General Security
```php
'security' => [
    'key' => 'your-secret-key-here', // 32+ random characters
    'salt' => 'your-salt-here', // 32+ random characters
    'hash_algo' => 'bcrypt', // bcrypt, argon2i, argon2id
    'bcrypt_cost' => 12,
],
```

### Session Configuration
```php
'security' => [
    // ... other settings
    'session' => [
        'name' => 'infinity_session',
        'lifetime' => 7200, // seconds (2 hours)
        'path' => '/',
        'domain' => '.yourdomain.com',
        'secure' => true, // HTTPS only
        'httponly' => true,
        'samesite' => 'Lax', // Strict, Lax, None
    ],
],
```

### Authentication
```php
'security' => [
    // ... other settings
    'auth' => [
        'max_attempts' => 5,
        'lockout_duration' => 900, // seconds (15 minutes)
        'password_min_length' => 8,
        'password_require_special' => true,
        'password_require_numbers' => true,
        'password_require_uppercase' => true,
        'two_factor_enabled' => false,
    ],
],
```

### CSRF Protection
```php
'security' => [
    // ... other settings
    'csrf' => [
        'enabled' => true,
        'token_name' => '_csrf_token',
        'header_name' => 'X-CSRF-Token',
        'cookie_name' => 'csrf_cookie',
        'expire' => 7200,
    ],
],
```

### Content Security Policy
```php
'security' => [
    // ... other settings
    'csp' => [
        'enabled' => true,
        'default-src' => ["'self'"],
        'script-src' => ["'self'", "'unsafe-inline'", 'https://cdn.jsdelivr.net'],
        'style-src' => ["'self'", "'unsafe-inline'", 'https://fonts.googleapis.com'],
        'img-src' => ["'self'", 'data:', 'https:'],
        'font-src' => ["'self'", 'https://fonts.gstatic.com'],
        'connect-src' => ["'self'"],
        'frame-ancestors' => ["'none'"],
    ],
],
```

## Email Configuration

### SMTP Settings
```php
'mail' => [
    'driver' => 'smtp', // smtp, mail, sendmail
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'encryption' => 'tls', // tls, ssl, or null
    'username' => 'your-email@gmail.com',
    'password' => 'your-password',
    'from' => [
        'address' => 'noreply@yourdomain.com',
        'name' => 'Your Site Name',
    ],
    'reply_to' => [
        'address' => 'support@yourdomain.com',
        'name' => 'Support',
    ],
],
```

### Mail Templates
```php
'mail' => [
    // ... other settings
    'templates' => [
        'path' => 'resources/mail',
        'cache' => true,
    ],
    'queue' => false, // Enable email queue
],
```

### Popular SMTP Services

#### Gmail
```php
'mail' => [
    'driver' => 'smtp',
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'encryption' => 'tls',
    'username' => 'your-email@gmail.com',
    'password' => 'your-app-password', // Use app password, not regular password
],
```

#### SendGrid
```php
'mail' => [
    'driver' => 'smtp',
    'host' => 'smtp.sendgrid.net',
    'port' => 587,
    'encryption' => 'tls',
    'username' => 'apikey',
    'password' => 'your-sendgrid-api-key',
],
```

#### Mailgun
```php
'mail' => [
    'driver' => 'smtp',
    'host' => 'smtp.mailgun.org',
    'port' => 587,
    'encryption' => 'tls',
    'username' => 'your-mailgun-username',
    'password' => 'your-mailgun-password',
],
```

## Cache Configuration

### Cache Drivers
```php
'cache' => [
    'driver' => 'file', // file, redis, memcached, apcu, array
    'default_ttl' => 3600, // seconds
    'prefix' => 'infinity_',
],
```

### File Cache
```php
'cache' => [
    'driver' => 'file',
    'path' => 'storage/cache',
    'permission' => 0755,
],
```

### Redis Cache
```php
'cache' => [
    'driver' => 'redis',
    'connection' => [
        'host' => '127.0.0.1',
        'port' => 6379,
        'password' => null,
        'database' => 0,
        'persistent' => false,
    ],
],
```

### Memcached
```php
'cache' => [
    'driver' => 'memcached',
    'servers' => [
        [
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 100,
        ],
    ],
],
```

### Cache Categories
```php
'cache' => [
    // ... driver settings
    'categories' => [
        'pages' => 3600,      // 1 hour
        'posts' => 1800,      // 30 minutes
        'widgets' => 7200,    // 2 hours
        'api' => 300,         // 5 minutes
        'queries' => 600,     // 10 minutes
    ],
],
```

## File Upload Settings

### Basic Upload Configuration
```php
'uploads' => [
    'path' => 'public/uploads',
    'url' => '/uploads',
    'max_size' => 10485760, // 10MB in bytes
    'allowed_types' => [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
        'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'],
        'video' => ['mp4', 'webm', 'ogg'],
        'audio' => ['mp3', 'wav', 'ogg'],
    ],
],
```

### Image Processing
```php
'uploads' => [
    // ... other settings
    'images' => [
        'quality' => 85,
        'max_width' => 2000,
        'max_height' => 2000,
        'thumbnails' => [
            'small' => [150, 150, 'crop'],
            'medium' => [300, 300, 'resize'],
            'large' => [1024, 1024, 'resize'],
        ],
        'webp_conversion' => true,
        'strip_metadata' => true,
    ],
],
```

### Storage Options
```php
'uploads' => [
    // ... other settings
    'storage' => [
        'driver' => 'local', // local, s3, ftp
        'organize_by_date' => true, // Organize in year/month folders
    ],
],

// Amazon S3 Configuration
'uploads' => [
    'storage' => [
        'driver' => 's3',
        'key' => 'your-aws-key',
        'secret' => 'your-aws-secret',
        'region' => 'us-east-1',
        'bucket' => 'your-bucket-name',
        'path' => 'uploads',
        'url' => 'https://your-bucket.s3.amazonaws.com',
    ],
],
```

## Advanced Configuration

### Performance
```php
'performance' => [
    'minify_html' => true,
    'minify_css' => true,
    'minify_js' => true,
    'combine_assets' => true,
    'lazy_load_images' => true,
    'cdn_url' => 'https://cdn.yourdomain.com',
    'preload_fonts' => true,
    'opcache_enabled' => true,
],
```

### API Configuration
```php
'api' => [
    'enabled' => true,
    'prefix' => 'api',
    'version' => 'v1',
    'rate_limit' => [
        'enabled' => true,
        'max_attempts' => 60,
        'decay_minutes' => 1,
    ],
    'cors' => [
        'enabled' => true,
        'origins' => ['*'],
        'methods' => ['GET', 'POST', 'PUT', 'DELETE'],
        'headers' => ['Content-Type', 'Authorization'],
    ],
],
```

### Logging
```php
'logging' => [
    'level' => 'error', // debug, info, warning, error, critical
    'driver' => 'file', // file, syslog, errorlog
    'path' => 'storage/logs',
    'max_files' => 30,
    'permissions' => 0644,
],
```

### Queue Configuration
```php
'queue' => [
    'driver' => 'database', // database, redis, sync
    'table' => 'jobs',
    'retry_after' => 90,
    'max_attempts' => 3,
],
```

### Search Configuration
```php
'search' => [
    'driver' => 'database', // database, elasticsearch, algolia
    'min_length' => 3,
    'stopwords' => ['the', 'and', 'or', 'but'],
    'fuzzy' => true,
    'boost' => [
        'title' => 2.0,
        'content' => 1.0,
        'excerpt' => 1.5,
    ],
],
```

## Environment Variables

You can use environment variables instead of hardcoding values:

### .env File
```bash
# Database
DB_HOST=localhost
DB_PORT=3306
DB_NAME=infinity_cms
DB_USER=root
DB_PASS=password

# Application
APP_NAME="My Website"
APP_URL=https://localhost
APP_ENV=development
APP_DEBUG=true
APP_KEY=your-secret-key

# Mail
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USER=your-email@gmail.com
MAIL_PASS=your-password
MAIL_ENCRYPTION=tls

# Cache
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Storage
STORAGE_DRIVER=s3
AWS_KEY=your-key
AWS_SECRET=your-secret
AWS_REGION=us-east-1
AWS_BUCKET=your-bucket
```

### Using Environment Variables
```php
// config.php
return [
    'database' => [
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'name' => $_ENV['DB_NAME'] ?? 'infinity_cms',
        'user' => $_ENV['DB_USER'] ?? 'root',
        'pass' => $_ENV['DB_PASS'] ?? '',
    ],
    'app' => [
        'name' => $_ENV['APP_NAME'] ?? 'Infinity CMS',
        'url' => $_ENV['APP_URL'] ?? 'http://localhost',
        'env' => $_ENV['APP_ENV'] ?? 'production',
        'debug' => $_ENV['APP_DEBUG'] ?? false,
    ],
];
```

## Configuration Best Practices

### Security
1. **Never commit config.php or .env files** to version control
2. **Use strong, unique keys** for security salts and keys
3. **Disable debug mode** in production
4. **Use HTTPS** in production
5. **Restrict file permissions** (config.php should be 640)

### Performance
1. **Enable caching** in production
2. **Use CDN** for static assets
3. **Optimize database queries** with proper indexing
4. **Enable OPcache** for PHP
5. **Use Redis or Memcached** for session storage

### Maintenance
1. **Keep backups** of configuration files
2. **Document custom settings**
3. **Use environment variables** for sensitive data
4. **Test configuration changes** in staging first
5. **Monitor logs** for configuration errors

## Troubleshooting Configuration Issues

### Database Connection Errors
```php
// Enable detailed error messages
'database' => [
    // ... other settings
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ],
],
```

### Debug Mode
```php
// Temporarily enable debug mode
'app' => [
    'debug' => true,
    'debug_bar' => true,
    'show_errors' => true,
],

// Log all queries
'database' => [
    'log_queries' => true,
],
```

### Cache Issues
```php
// Disable cache temporarily
'cache' => [
    'driver' => 'array', // In-memory only, no persistence
],
```

## Configuration Validation

### Health Check Endpoint
Create `/health` endpoint to verify configuration:
```php
// routes/web.php
Route::get('/health', function() {
    $checks = [
        'database' => check_database_connection(),
        'cache' => check_cache_connection(),
        'mail' => check_mail_configuration(),
        'storage' => check_storage_permissions(),
    ];

    return response()->json($checks);
});
```

### Configuration Test Script
```php
// test-config.php
<?php
require 'bootstrap/app.php';

echo "Testing configuration...\n\n";

// Test database
try {
    db()->query("SELECT 1");
    echo "✓ Database connection successful\n";
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
}

// Test cache
try {
    cache()->set('test', 'value', 60);
    $value = cache()->get('test');
    echo "✓ Cache working\n";
} catch (Exception $e) {
    echo "✗ Cache error: " . $e->getMessage() . "\n";
}

// Test mail
try {
    // Send test email
    echo "✓ Mail configuration valid\n";
} catch (Exception $e) {
    echo "✗ Mail error: " . $e->getMessage() . "\n";
}
```

## Migration from Other Systems

### WordPress Migration
```php
// config.php adjustments for WordPress migration
'migration' => [
    'from' => 'wordpress',
    'wp_prefix' => 'wp_',
    'maintain_ids' => true,
    'redirect_old_urls' => true,
],
```

### Joomla Migration
```php
'migration' => [
    'from' => 'joomla',
    'joomla_prefix' => 'jos_',
    'maintain_users' => true,
],
```

## Support

For configuration help:
- Check the [FAQ](FAQ.md)
- Visit the [Community Forum](https://community.infinity-cms.com)
- Review [Common Issues](TROUBLESHOOTING.md)
- Contact support for enterprise configurations