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
        // Debug: Check configuration status
        $step1 = $this->isStep1Configured();
        $step2 = $this->isStep2Configured();
        $step3 = $this->isStep3Configured();
        $isConfigured = $this->isConfigured();

        // Check if already configured
        if ($isConfigured) {
            redirect(url('/'));
            return;
        }

        $step = request()->get('step', '1');

        // Validate step value
        if (!in_array($step, ['1', '2', '3'])) {
            $step = '1';
        }

        $data = [
            'title' => 'Setup - Infinity CMS',
            'step' => $step,
            'errors' => session('errors', []),
            'old' => session('old', []),
        ];

        // Try to load existing configuration for pre-filling (if config exists)
        // This allows reconfiguration without showing error messages
        $envPath = root_path('config.php');
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
        } elseif ($step === '3') {
            return $this->processStep3();
        }

        return $this->index();
    }

    /**
     * Process Step 1: Database Setup
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


        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            // Store old values for form repopulation
            $_SESSION['old'] = $data;
            redirect(url('/setup?step=1'));
            return;
        }

        // Test database connection and run migrations
        try {
            // Reload database connection with new configuration
            $this->reloadDatabase($data);

            // Run database migrations to create tables
            $migrationResult = $this->runMigrations();

            // Check if migrations failed
            if (!$migrationResult['success']) {
                // Store migration errors in session
                $_SESSION['migration_errors'] = $migrationResult;
                $errors['general'] = 'Database setup failed. ' . $migrationResult['failureCount'] . ' migration(s) failed. See details below.';
                $_SESSION['errors'] = $errors;

                // Store old values for form repopulation
                $_SESSION['old'] = $data;

                redirect(url('/setup?step=1'));
                return;
            }

            // Store in session for step 2
            $_SESSION['setup_config'] = $data;
            $_SESSION['setup_step1_complete'] = true;
            unset($_SESSION['errors'], $_SESSION['old'], $_SESSION['migration_errors']);

            redirect(url('/setup?step=2'));

        } catch (\Exception $e) {
            $_SESSION['errors'] = ['general' => 'Setup failed: ' . $e->getMessage()];
            $_SESSION['old'] = $data;
            redirect(url('/setup?step=1'));
        }
    }

    /**
     * Process Step 2: Admin Account Creation
     */
    protected function processStep2() {
        if (!isset($_SESSION['setup_config'])) {
            redirect(url('/setup?step=1'));
            return;
        }

        $config = $_SESSION['setup_config'];

        // Admin account configuration
        $adminOption = request()->post('admin_option', 'custom');
        $config['admin_option'] = $adminOption;

        if ($adminOption === 'custom') {
            $config['admin_username'] = request()->post('admin_username');
            $config['admin_email'] = request()->post('admin_email');
            $config['admin_password'] = request()->post('admin_password');
            $password_confirm = request()->post('admin_password_confirm');

            // Validation
            $errors = [];

            if (empty($config['admin_username'])) {
                $errors['admin_username'] = 'Username is required';
            }
            if (empty($config['admin_email'])) {
                $errors['admin_email'] = 'Email is required';
            } elseif (!filter_var($config['admin_email'], FILTER_VALIDATE_EMAIL)) {
                $errors['admin_email'] = 'Please enter a valid email';
            }
            if (empty($config['admin_password'])) {
                $errors['admin_password'] = 'Password is required';
            } elseif (strlen($config['admin_password']) < 8) {
                $errors['admin_password'] = 'Password must be at least 8 characters';
            }
            if ($config['admin_password'] !== $password_confirm) {
                $errors['admin_password_confirm'] = 'Passwords do not match';
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $oldData = $config;
                unset($oldData['admin_password']);
                $_SESSION['old'] = $oldData;
                redirect(url('/setup?step=2'));
                return;
            }
        } elseif ($adminOption === 'default') {
            // Use default admin credentials
            $config['admin_username'] = 'admin';
            $config['admin_email'] = 'admin@example.com';
            $config['admin_password'] = 'admin123';
        }
        // If 'skip' is selected, we don't create any user

        // Store updated config in session
        $_SESSION['setup_config'] = $config;
        $_SESSION['setup_step2_complete'] = true;

        // Proceed to step 3
        redirect(url('/setup?step=3'));
    }

    /**
     * Process Step 3: Site Settings
     */
    protected function processStep3() {
        if (!isset($_SESSION['setup_config'])) {
            redirect(url('/setup?step=1'));
            return;
        }

        $config = $_SESSION['setup_config'];

        // Preserve admin option
        $adminOption = $config['admin_option'] ?? 'custom';

        // Site settings from step 3
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
            redirect(url('/setup?step=3'));
            return;
        }

        // Write final configuration file with all setup data
        try {
            $this->writeRootConfigFile();

            // Reload environment to use updated config
            $this->reloadDatabase($config);

            // Publish theme assets
            $this->publishAssets($config['theme']);

            // Create admin user (migrations already ran in step 1)
            $this->createAdminUser($config);

            // Mark step 3 as complete
            $_SESSION['setup_step3_complete'] = true;

            // Clear setup session
            unset(
                $_SESSION['setup_config'],
                $_SESSION['setup_step1_complete'],
                $_SESSION['setup_step2_complete'],
                $_SESSION['setup_step3_complete'],
                $_SESSION['errors'],
                $_SESSION['old'],
                $_SESSION['migration_errors']
            );

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
            redirect(url('/setup?step=3'));
        }
    }

    /**
     * Write root config.php file using data from setup session
     */
    protected function writeRootConfigFile() {
        // Get all setup data from session
        if (!isset($_SESSION['setup_config'])) {
            throw new \Exception('Setup configuration not found in session');
        }

        $config = $_SESSION['setup_config'];
        $envPath = root_path('config.php');

        // Extract values from config
        $appName = $config['app_name'] ?? 'Infinity CMS';
        $appUrl = $config['app_url'] ?? '';
        $theme = $config['theme'] ?? 'infinity';
        $timezone = $config['timezone'] ?? 'UTC';

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
        'name' => '{$appName}',
        'url' => '{$appUrl}',
        'debug' => true,
        'theme' => '{$theme}',
        'timezone' => '{$timezone}',
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
        $hasErrors = false;
        $errorMessages = [];
        $successCount = 0;
        $failureCount = 0;

        foreach ($results as $result) {
            if (strpos($result, '✗') !== false) {
                $hasErrors = true;
                $failureCount++;
                $errorMessages[] = $result;
            } elseif (strpos($result, '✓') !== false) {
                $successCount++;
            }
        }

        // If there are errors, return detailed information
        if ($hasErrors) {
            return [
                'success' => false,
                'successCount' => $successCount,
                'failureCount' => $failureCount,
                'results' => $results,
                'errors' => $errorMessages
            ];
        }

        return [
            'success' => true,
            'successCount' => $successCount,
            'results' => $results
        ];
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
     * Check if Step 1 (Database Setup) is configured
     */
    protected function isStep1Configured() {
        // Check session flag first
        return isset($_SESSION['setup_step1_complete']);
    }

    /**
     * Check if Step 2 (Admin Account) is configured
     */
    protected function isStep2Configured() {
        // Check session flag
        return isset($_SESSION['setup_step2_complete']);
    }

    /**
     * Check if Step 3 (Site Settings) is configured
     */
    protected function isStep3Configured() {
        // Check session flag
        return isset($_SESSION['setup_step3_complete']);
    }

    /**
     * Check if application is fully configured
     * All 3 steps must be complete
     */
    protected function isConfigured() {
        // If CONFIG_MISSING is defined and true, definitely not configured
        if (defined('CONFIG_MISSING') && CONFIG_MISSING === true) {
            return false;
        }

        // Check if there's a database connection error stored
        $app = \App\Core\App::getInstance();
        if ($app->has('db_connection_error')) {
            return false;
        }

        // All three steps must be configured
        return $this->isStep1Configured()
            && $this->isStep2Configured()
            && $this->isStep3Configured();
    }
}
