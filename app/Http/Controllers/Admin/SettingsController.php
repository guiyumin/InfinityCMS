<?php

namespace App\Http\Controllers\Admin;

/**
 * Admin Settings Controller
 * Manages CRUD operations for system settings
 */
class SettingsController {
    /**
     * Display all settings
     *
     * @return string
     */
    public function index() {
        // Check if settings table exists
        if (!$this->tableExists('settings')) {
            $_SESSION['errors'] = ['The settings table does not exist. Please run migrations first.'];
            return admin_view('settings.error', [
                'title' => 'Settings',
                'error' => 'The settings table does not exist.',
                'message' => 'Please go to the Migrations page and run the migrations to create the settings table.',
            ]);
        }

        $settings = db()->table('settings')
            ->orderBy('setting_key', 'ASC')
            ->get();

        return admin_view('settings.index', [
            'title' => 'Settings',
            'settings' => $settings,
        ]);
    }

    /**
     * Show create setting form
     *
     * @return string
     */
    public function create() {
        if (!$this->tableExists('settings')) {
            $_SESSION['errors'] = ['The settings table does not exist. Please run migrations first.'];
            app('response')->redirect('/admin/settings');
            return;
        }

        return admin_view('settings.create', [
            'title' => 'Create New Setting',
        ]);
    }

    /**
     * Store a new setting
     *
     * @return void
     */
    public function store() {
        if (!$this->tableExists('settings')) {
            $_SESSION['errors'] = ['The settings table does not exist. Please run migrations first.'];
            app('response')->redirect('/admin/settings');
            return;
        }

        $data = request()->all();

        // Validate required fields
        $errors = [];
        if (empty($data['setting_key'])) {
            $errors[] = 'Setting key is required';
        }
        if (!isset($data['setting_value'])) {
            $errors[] = 'Setting value is required';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['_old'] = $data;
            app('response')->redirect('/admin/settings/create');
            return;
        }

        // Check if setting key already exists
        $existing = db()->table('settings')
            ->where('setting_key', $data['setting_key'])
            ->first();

        if ($existing) {
            $_SESSION['errors'] = ['Setting key already exists'];
            $_SESSION['_old'] = $data;
            app('response')->redirect('/admin/settings/create');
            return;
        }

        // Insert setting
        db()->table('settings')->insert([
            'setting_key' => $data['setting_key'],
            'setting_value' => $data['setting_value'],
            'description' => $data['description'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $_SESSION['success'] = 'Setting created successfully';
        app('response')->redirect('/admin/settings');
    }

    /**
     * Show edit setting form
     *
     * @return string
     */
    public function edit() {
        if (!$this->tableExists('settings')) {
            $_SESSION['errors'] = ['The settings table does not exist. Please run migrations first.'];
            app('response')->redirect('/admin/settings');
            return;
        }

        $id = request()->get('id');

        $setting = db()->table('settings')
            ->where('id', $id)
            ->first();

        if (!$setting) {
            $_SESSION['errors'] = ['Setting not found'];
            app('response')->redirect('/admin/settings');
            return;
        }

        return admin_view('settings.edit', [
            'title' => 'Edit Setting',
            'setting' => $setting,
        ]);
    }

    /**
     * Update a setting
     *
     * @return void
     */
    public function update() {
        if (!$this->tableExists('settings')) {
            $_SESSION['errors'] = ['The settings table does not exist. Please run migrations first.'];
            app('response')->redirect('/admin/settings');
            return;
        }

        $id = request()->get('id');
        $data = request()->all();

        // Validate required fields
        $errors = [];
        if (!isset($data['setting_value'])) {
            $errors[] = 'Setting value is required';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            app('response')->redirect('/admin/settings/edit?id=' . $id);
            return;
        }

        // Update setting
        db()->table('settings')
            ->where('id', $id)
            ->update([
                'setting_value' => $data['setting_value'],
                'description' => $data['description'] ?? null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $_SESSION['success'] = 'Setting updated successfully';
        app('response')->redirect('/admin/settings');
    }

    /**
     * Delete a setting
     *
     * @return void
     */
    public function delete() {
        if (!$this->tableExists('settings')) {
            $_SESSION['errors'] = ['The settings table does not exist. Please run migrations first.'];
            app('response')->redirect('/admin/settings');
            return;
        }

        $id = request()->get('id');

        db()->table('settings')
            ->where('id', $id)
            ->delete();

        $_SESSION['success'] = 'Setting deleted successfully';
        app('response')->redirect('/admin/settings');
    }

    /**
     * Check if a table exists in the database
     *
     * @param string $tableName
     * @return bool
     */
    protected function tableExists($tableName) {
        try {
            $tables = db()->query("SHOW TABLES LIKE ?", [$tableName]);
            return !empty($tables);
        } catch (\Exception $e) {
            return false;
        }
    }
}
