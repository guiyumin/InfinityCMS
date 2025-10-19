<?php

namespace App\Http\Controllers\Admin;

use App\Core\AssetPublisher;

/**
 * Assets Controller
 * Manages theme asset publishing via web interface
 */
class AssetsController {

    /**
     * Show asset management page
     */
    public function index() {
        // Get list of available themes
        $themesPath = base_path('themes');
        $themes = [];

        if (is_dir($themesPath)) {
            $items = scandir($themesPath);
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }

                $themeDir = $themesPath . '/' . $item;
                if (is_dir($themeDir)) {
                    $assetsPath = $themeDir . '/assets';
                    $publishedPath = base_path("public/themes/{$item}/assets");

                    $themes[] = [
                        'name' => $item,
                        'has_assets' => is_dir($assetsPath),
                        'is_published' => is_dir($publishedPath),
                        'is_current' => $item === config('app.theme'),
                    ];
                }
            }
        }

        // Check admin assets status
        $adminAssetsPath = base_path('app/Views/admin/assets');
        $adminPublishedPath = base_path('public/admin/assets');

        $data = [
            'title' => 'Asset Management',
            'themes' => $themes,
            'admin_has_assets' => is_dir($adminAssetsPath),
            'admin_is_published' => is_dir($adminPublishedPath),
            'success' => session('success'),
            'error' => session('error'),
        ];

        return admin_view('assets.index', $data);
    }

    /**
     * Publish assets for a specific theme
     */
    public function publish() {
        $theme = request()->post('theme');
        $force = request()->post('force') === 'true';

        if (!$theme) {
            flash('error', 'Theme name is required');
            redirect(url('/admin/assets'));
            return;
        }

        $result = AssetPublisher::publish($theme, $force);

        if ($result['success']) {
            flash('success', $result['message']);
        } else {
            flash('error', $result['message']);
        }

        redirect(url('/admin/assets'));
    }

    /**
     * Publish all theme assets and admin assets
     */
    public function publishAll() {
        $force = request()->post('force') === 'true';

        // Publish all assets (themes + admin)
        $results = AssetPublisher::publishAll($force, true);

        $successCount = 0;
        $failCount = 0;
        $messages = [];

        foreach ($results as $name => $result) {
            if ($result['success']) {
                $successCount++;
            } else {
                $failCount++;
                $messages[] = "{$name}: {$result['message']}";
            }
        }

        if ($failCount > 0) {
            $errorMsg = "Published {$successCount} successfully, {$failCount} failed. Errors: " . implode('; ', $messages);
            flash('error', $errorMsg);
        } else {
            flash('success', "Successfully published assets for {$successCount} items (themes + admin)");
        }

        redirect(url('/admin/assets'));
    }

    /**
     * Clean published assets for a theme
     */
    public function clean() {
        $theme = request()->post('theme');

        if (!$theme) {
            flash('error', 'Theme name is required');
            redirect(url('/admin/assets'));
            return;
        }

        $result = AssetPublisher::clean($theme);

        if ($result['success']) {
            flash('success', $result['message']);
        } else {
            flash('error', $result['message']);
        }

        redirect(url('/admin/assets'));
    }
}
