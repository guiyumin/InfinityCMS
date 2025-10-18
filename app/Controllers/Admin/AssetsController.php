<?php

namespace App\Controllers\Admin;

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

        $data = [
            'title' => 'Asset Management',
            'themes' => $themes,
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
     * Publish all theme assets
     */
    public function publishAll() {
        $force = request()->post('force') === 'true';
        $results = AssetPublisher::publishAll($force);

        $successCount = 0;
        $failCount = 0;
        $messages = [];

        foreach ($results as $theme => $result) {
            if ($result['success']) {
                $successCount++;
            } else {
                $failCount++;
                $messages[] = "{$theme}: {$result['message']}";
            }
        }

        if ($failCount > 0) {
            $errorMsg = "Published {$successCount} themes successfully, {$failCount} failed. Errors: " . implode('; ', $messages);
            flash('error', $errorMsg);
        } else {
            flash('success', "Successfully published assets for {$successCount} themes");
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
