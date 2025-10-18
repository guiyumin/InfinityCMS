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

        return view('setup.index', $data, 'setup');
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
     * Process Step 1: Basic Configuration
     */
    protected function processStep1() {
        $data = [
            'app_name' => request()->post('app_name'),
            'app_url' => rtrim(request()->post('app_url'), '/'),
            'timezone' => request()->post('timezone', 'UTC'),
            'theme' => request()->post('theme', 'infinity'),
        ];

        // Validation
        $errors = [];
        if (empty($data['app_name'])) {
            $errors['app_name'] = 'Application name is required';
        }
        if (empty($data['app_url'])) {
            $errors['app_url'] = 'Application URL is required';
        } elseif (!filter_var($data['app_url'], FILTER_VALIDATE_URL)) {
            $errors['app_url'] = 'Please enter a valid URL';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            redirect(url('/setup?step=1'));
            return;
        }

        // Store in session for step 2
        $_SESSION['setup_config'] = $data;
        unset($_SESSION['errors'], $_SESSION['old']);

        redirect(url('/setup?step=2'));
    }

    /**
     * Process Step 2: Database & Admin Account
     */
    protected function processStep2() {
        if (!isset($_SESSION['setup_config'])) {
            redirect(url('/setup?step=1'));
            return;
        }

        $config = $_SESSION['setup_config'];

        // Database configuration
        $config['db_driver'] = request()->post('db_driver', 'sqlite');

        if ($config['db_driver'] === 'mysql') {
            $config['db_host'] = request()->post('db_host');
            $config['db_port'] = request()->post('db_port', '3306');
            $config['db_name'] = request()->post('db_name');
            $config['db_user'] = request()->post('db_user');
            $config['db_pass'] = request()->post('db_pass');
        }

        // Admin account
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
            redirect(url('/setup?step=2'));
            return;
        }

        // Write configuration file
        try {
            $this->writeEnvFile($config);

            // Publish theme assets
            $this->publishAssets($config['theme']);

            // Create admin user (if database is ready)
            $this->createAdminUser($config);

            // Clear setup session
            unset($_SESSION['setup_config'], $_SESSION['errors'], $_SESSION['old']);

            // Set success message
            flash('success', 'Setup completed successfully! You can now log in with your admin account.');

            redirect(url('/login'));
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['general' => 'Setup failed: ' . $e->getMessage()];
            redirect(url('/setup?step=2'));
        }
    }

    /**
     * Write .env.php configuration file
     */
    protected function writeEnvFile($config) {
        $envPath = base_path('.env.php');

        // Database configuration
        if ($config['db_driver'] === 'sqlite') {
            $dbConfig = <<<PHP
        'driver' => 'sqlite',
        'path' => __DIR__ . '/storage/database.sqlite',
PHP;
        } else {
            $dbConfig = <<<PHP
        'driver' => 'mysql',
        'host' => '{$config['db_host']}',
        'port' => {$config['db_port']},
        'database' => '{$config['db_name']}',
        'username' => '{$config['db_user']}',
        'password' => '{$config['db_pass']}',
        'charset' => 'utf8mb4',
PHP;
        }

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
     * Publish theme assets to public folder
     */
    protected function publishAssets($theme) {
        AssetPublisher::publish($theme, true);
    }

    /**
     * Create admin user
     */
    protected function createAdminUser($config) {
        try {
            // Check if users table exists
            $users = db()->table('users')->limit(1)->get();

            // Create admin user
            db()->table('users')->insert([
                'username' => $config['admin_username'],
                'email' => $config['admin_email'],
                'password' => password_hash($config['admin_password'], PASSWORD_DEFAULT),
                'role' => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            // Database might not be ready yet, skip for now
            // Admin can be created through migrations
        }
    }

    /**
     * Check if application is already configured
     */
    protected function isConfigured() {
        $envPath = base_path('.env.php');

        if (!file_exists($envPath)) {
            return false;
        }

        $config = include $envPath;

        // Check if URL is empty or still localhost (default/unconfigured)
        if (!isset($config['app']['url']) ||
            empty($config['app']['url']) ||
            $config['app']['url'] === 'http://localhost') {
            return false;
        }

        return true;
    }
}
