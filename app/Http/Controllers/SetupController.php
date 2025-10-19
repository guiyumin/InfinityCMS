<?php

namespace App\Http\Controllers;

use App\Core\AssetPublisher;

/**
 * Setup Controller
 * Handles initial application setup and configuration
 */
class SetupController {

    /**
     * Show setup wizard
     */
    public function index() {
        // Check if already configured
        if ($this->isConfigured()) {
            redirect(url('/'));
            return;
        }

        $data = [
            'title' => 'Setup - Infinity CMS',
            'step' => request()->get('step', '1'),
            'errors' => session('errors', []),
            'old' => session('old', []),
        ];

        // Try to load existing configuration for pre-filling (if config exists)
        // This allows reconfiguration without showing error messages
        $envPath = base_path('config.php');
        if (file_exists($envPath)) {
            $config = include $envPath;
            if (isset($config['database']) && empty($data['old'])) {
                // Pre-fill with existing values (except password) only if no form data exists
                $data['old']['db_host'] = $config['database']['host'] ?? 'localhost';
                $data['old']['db_port'] = $config['database']['port'] ?? '3306';
                $data['old']['db_name'] = $config['database']['database'] ?? '';
                $data['old']['db_user'] = $config['database']['username'] ?? '';
            }
            if (isset($config['app']) && empty($data['old'])) {
                $data['old']['app_name'] = $config['app']['name'] ?? '';
                $data['old']['app_url'] = $config['app']['url'] ?? '';
                $data['old']['timezone'] = $config['app']['timezone'] ?? 'UTC';
            }
        }

        // Clear errors and old input after reading them (flash behavior)
        unset($_SESSION['errors'], $_SESSION['old']);

        return setup_view('index', $data);
    }

    /**
     * Process setup form
     */
    public function process() {
        $step = request()->post('step', '1');

        if ($step === '1') {
            return $this->processStep1();
        } elseif ($step === '2') {
            return $this->processStep2();
        }

        return $this->index();
    }

    /**
     * Process Step 1: Database & Admin Account
     */
    protected function processStep1() {
        $data = [];

        // MySQL Database configuration (always MySQL)
        $data['db_driver'] = 'mysql';
        $data['db_host'] = request()->post('db_host');
        $data['db_port'] = request()->post('db_port', '3306');
        $data['db_name'] = request()->post('db_name');
        $data['db_user'] = request()->post('db_user');
        $data['db_pass'] = request()->post('db_pass', '');

        // Get admin account option
        $adminOption = request()->post('admin_option', 'custom');
        $data['admin_option'] = $adminOption;

        if ($adminOption === 'default') {
            // Use default credentials
            $data['admin_username'] = 'admin';
            $data['admin_email'] = 'admin@example.com';
            $data['admin_password'] = 'admin123';
        } elseif ($adminOption === 'skip') {
            // Skip admin creation - user already has admin in DB
            $data['admin_username'] = null;
            $data['admin_email'] = null;
            $data['admin_password'] = null;
        } else {
            // Get custom admin account details
            $data['admin_username'] = request()->post('admin_username');
            $data['admin_email'] = request()->post('admin_email');
            $data['admin_password'] = request()->post('admin_password');
            $password_confirm = request()->post('admin_password_confirm');
        }

        // Validation
        $errors = [];

        // Validate MySQL configuration
        if (empty($data['db_host'])) {
            $errors['db_host'] = 'Database host is required';
        }
        if (empty($data['db_name'])) {
            $errors['db_name'] = 'Database name is required';
        }
        if (empty($data['db_user'])) {
            $errors['db_user'] = 'Database username is required';
        }

        // Test MySQL connection if basic validation passes
        if (empty($errors)) {
            $connectionTest = $this->testMySQLConnection(
                $data['db_host'],
                $data['db_port'],
                $data['db_name'],
                $data['db_user'],
                $data['db_pass']
            );

            if (!$connectionTest['success']) {
                $errors['general'] = $connectionTest['message'];
            }
        }

        // Validate admin account only for custom option
        if ($adminOption === 'custom') {
            if (empty($data['admin_username'])) {
                $errors['admin_username'] = 'Username is required';
            }
            if (empty($data['admin_email'])) {
                $errors['admin_email'] = 'Email is required';
            } elseif (!filter_var($data['admin_email'], FILTER_VALIDATE_EMAIL)) {
                $errors['admin_email'] = 'Please enter a valid email';
            }
            if (empty($data['admin_password'])) {
                $errors['admin_password'] = 'Password is required';
            } elseif (strlen($data['admin_password']) < 8) {
                $errors['admin_password'] = 'Password must be at least 8 characters';
            }
            if ($data['admin_password'] !== $password_confirm) {
                $errors['admin_password_confirm'] = 'Passwords do not match';
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            // Store old values (except password) for form repopulation
            $oldData = $data;
            unset($oldData['admin_password']); // Don't store password in session
            $_SESSION['old'] = $oldData;
            redirect(url('/setup?step=1'));
            return;
        }

        // Store in session for step 2
        $_SESSION['setup_config'] = $data;
        unset($_SESSION['errors'], $_SESSION['old']);

        redirect(url('/setup?step=2'));
    }

    /**
     * Process Step 2: Site Settings
     */
    protected function processStep2() {
        if (!isset($_SESSION['setup_config'])) {
            redirect(url('/setup?step=1'));
            return;
        }

        $config = $_SESSION['setup_config'];

        // Preserve admin option
        $adminOption = $config['admin_option'] ?? 'custom';

        // Site settings from step 2
        $config['app_name'] = request()->post('app_name');
        $config['app_url'] = rtrim(request()->post('app_url'), '/');
        $config['timezone'] = request()->post('timezone', 'UTC');
        $config['theme'] = request()->post('theme', 'infinity');

        // Validation
        $errors = [];
        if (empty($config['app_name'])) {
            $errors['app_name'] = 'Application name is required';
        }
        if (empty($config['app_url'])) {
            $errors['app_url'] = 'Application URL is required';
        } elseif (!filter_var($config['app_url'], FILTER_VALIDATE_URL)) {
            $errors['app_url'] = 'Please enter a valid URL';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $config;
            redirect(url('/setup?step=2'));
            return;
        }

        // Write configuration file
        try {
            $this->writeEnvFile($config);

            // Reload environment to use new database config
            // We need to reconnect to database with new credentials
            $this->reloadDatabase($config);

            // Run database migrations to create tables
            $this->runMigrations();

            // Publish theme assets
            $this->publishAssets($config['theme']);

            // Create admin user
            $this->createAdminUser($config);

            // Clear setup session
            unset($_SESSION['setup_config'], $_SESSION['errors'], $_SESSION['old']);

            // Set success message based on admin option
            if ($adminOption === 'default') {
                flash('success', 'Setup completed! Login with username: admin, password: admin123. Please change these credentials after logging in.');
            } elseif ($adminOption === 'skip') {
                flash('success', 'Setup completed! You can now log in with your existing admin account.');
            } else {
                flash('success', 'Setup completed successfully! You can now log in with your admin account.');
            }

            redirect(url('/login'));
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['general' => 'Setup failed: ' . $e->getMessage()];
            redirect(url('/setup?step=2'));
        }
    }

    /**
     * Write config.php configuration file
     */
    protected function writeEnvFile($config) {
        $envPath = base_path('config.php');

        // MySQL Database configuration (always MySQL)
        $dbConfig = <<<PHP
        'driver' => 'mysql',
        'host' => '{$config['db_host']}',
        'port' => {$config['db_port']},
        'database' => '{$config['db_name']}',
        'username' => '{$config['db_user']}',
        'password' => '{$config['db_pass']}',
        'charset' => 'utf8mb4',
PHP;

        $content = <<<PHP
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
        'name' => '{$config['app_name']}',
        'url' => '{$config['app_url']}',
        'debug' => true,
        'theme' => '{$config['theme']}',
        'timezone' => '{$config['timezone']}',
    ],

    // 数据库配置
    'database' => [
{$dbConfig}
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

PHP;

        if (file_put_contents($envPath, $content) === false) {
            throw new \Exception('Failed to write configuration file');
        }
    }

    /**
     * Reload database connection with new configuration
     */
    protected function reloadDatabase($config) {
        // Reconnect to database with new credentials
        $dbConfig = [
            'driver' => 'mysql',
            'host' => $config['db_host'],
            'port' => $config['db_port'],
            'database' => $config['db_name'],
            'username' => $config['db_user'],
            'password' => $config['db_pass'],
            'charset' => 'utf8mb4',
        ];

        // Rebind database with new connection
        $app = \App\Core\App::getInstance();
        $app->bind('db', new \App\Core\DB($dbConfig));
    }

    /**
     * Run database migrations
     */
    protected function runMigrations() {
        $migration = new \App\Core\Migration();
        $results = $migration->run();

        // Check for any failed migrations
        foreach ($results as $result) {
            if (strpos($result, '✗ Failed') !== false) {
                throw new \Exception('Migration failed: ' . $result);
            }
        }
    }

    /**
     * Publish theme and admin assets to public folder
     */
    protected function publishAssets($theme) {
        // Publish theme assets
        AssetPublisher::publish($theme, true);

        // Publish admin assets
        AssetPublisher::publish('admin', true);
    }

    /**
     * Create admin user
     */
    protected function createAdminUser($config) {
        // Skip admin creation if user chose 'skip' option
        $adminOption = $config['admin_option'] ?? 'custom';
        if ($adminOption === 'skip') {
            return;
        }

        try {
            // Create admin user (migrations table should exist now)
            db()->table('users')->insert([
                'username' => $config['admin_username'],
                'email' => $config['admin_email'],
                'password' => password_hash($config['admin_password'], PASSWORD_DEFAULT),
                'role' => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            // If user already exists (from migration seed), that's okay
            // Just continue
        }
    }

    /**
     * Test MySQL database connection
     *
     * @param string $host
     * @param int $port
     * @param string $database
     * @param string $username
     * @param string $password
     * @return array
     */
    protected function testMySQLConnection($host, $port, $database, $username, $password) {
        try {
            // Check if PDO MySQL driver is available
            if (!extension_loaded('pdo_mysql')) {
                return [
                    'success' => false,
                    'message' => 'MySQL PDO extension is not installed. Please install php-mysql extension.',
                ];
            }

            // Attempt connection
            $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_TIMEOUT => 5, // 5 second timeout
            ]);

            // Check if database exists
            $stmt = $pdo->query("SHOW DATABASES LIKE '{$database}'");
            $dbExists = $stmt->rowCount() > 0;

            if (!$dbExists) {
                return [
                    'success' => false,
                    'message' => "Database '{$database}' does not exist. Please create it first or use an existing database.",
                ];
            }

            // Try to use the database
            $pdo->exec("USE `{$database}`");

            return [
                'success' => true,
                'message' => 'MySQL connection successful',
            ];

        } catch (\PDOException $e) {
            // Parse common error messages
            $message = $e->getMessage();

            if (strpos($message, 'Access denied') !== false) {
                return [
                    'success' => false,
                    'message' => 'Database Access Denied: Invalid username or password.',
                ];
            } elseif (strpos($message, 'Unknown database') !== false) {
                return [
                    'success' => false,
                    'message' => "Database '{$database}' does not exist. Please create it first.",
                ];
            } elseif (strpos($message, "Can't connect") !== false || strpos($message, 'Connection refused') !== false) {
                return [
                    'success' => false,
                    'message' => "Cannot connect to MySQL server at {$host}:{$port}. Please check if MySQL is running.",
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Database connection failed: ' . $message,
                ];
            }
        }
    }

    /**
     * Check if application is already configured
     */
    protected function isConfigured() {
        // If CONFIG_MISSING is defined and true, definitely not configured
        if (defined('CONFIG_MISSING') && CONFIG_MISSING === true) {
            return false;
        }

        $envPath = base_path('config.php');

        if (!file_exists($envPath)) {
            return false;
        }

        $config = include $envPath;

        // Check if URL is empty or still has placeholder values
        if (!isset($config['app']['url']) ||
            empty($config['app']['url']) ||
            $config['app']['url'] === 'https://your-domain.com' ||
            $config['app']['url'] === 'http://localhost') {
            return false;
        }

        // Check if database has placeholder values
        if (!isset($config['database']['database']) ||
            empty($config['database']['database']) ||
            $config['database']['database'] === 'your_database_name' ||
            $config['database']['username'] === 'your_database_user') {
            return false;
        }

        // Check if there's a database connection error stored
        $app = \App\Core\App::getInstance();
        if ($app->has('db_connection_error')) {
            // If we have CONFIG_MISSING, allow setup
            // Otherwise it's a real error (config exists but DB connection failed)
            return false;
        }

        // Try to verify database connection actually works
        try {
            $db = db();
            if (!$db->getPdo()) {
                return false; // No connection
            }

            // Try a simple query to check if database is accessible
            $db->query("SELECT 1");

            // Check if users table exists
            $users = $db->query("SELECT COUNT(*) as count FROM users");
            if ($users && $users[0]['count'] > 0) {
                return true; // Fully configured
            }

            return false; // Tables not setup yet
        } catch (\Exception $e) {
            // Database not accessible or tables don't exist
            return false;
        }
    }
}
