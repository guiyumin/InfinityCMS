<?php

namespace App\Core;

/**
 * Asset Publisher
 * Publishes theme assets to public folder
 */
class AssetPublisher {

    /**
     * Publish assets for a specific theme or admin
     *
     * @param string|null $theme Theme name, or null for admin assets
     * @param bool $force Force republish even if assets exist
     * @return array Result with success status and message
     */
    public static function publish($theme = null, $force = false) {
        // Determine if publishing admin or theme assets
        if ($theme === 'admin' || $theme === null) {
            $sourcePath = root_path("app/Views/admin/assets");
            $targetPath = root_path("public/assets/admin");
            $assetType = 'Admin';
        } else {
            $sourcePath = root_path("themes/{$theme}/assets");
            $targetPath = root_path("public/assets/themes/{$theme}");
            $assetType = "Theme '{$theme}'";
        }

        // Check if source exists
        if (!is_dir($sourcePath)) {
            return [
                'success' => false,
                'message' => "{$assetType} assets not found at {$sourcePath}",
            ];
        }

        // Delete existing assets if forcing
        if ($force && is_dir($targetPath)) {
            self::deleteDirectory($targetPath);
        }

        // Create target directory
        if (!is_dir(dirname($targetPath))) {
            mkdir(dirname($targetPath), 0755, true);
        }

        // Copy assets
        try {
            self::copyDirectory($sourcePath, $targetPath);

            return [
                'success' => true,
                'message' => "Assets for {$assetType} published successfully",
                'path' => $targetPath,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "Failed to publish assets: " . $e->getMessage(),
            ];
        }
    }

    /**
     * Publish all theme assets and admin assets
     *
     * @param bool $force Force republish even if assets exist
     * @param bool $includeAdmin Include admin assets in publish (default: true)
     * @return array Results for each theme/admin
     */
    public static function publishAll($force = false, $includeAdmin = true) {
        $results = [];

        // Publish admin assets first
        if ($includeAdmin) {
            $results['admin'] = self::publish('admin', $force);
        }

        // Publish theme assets
        $themesPath = root_path('themes');
        if (!is_dir($themesPath)) {
            return $results;
        }

        $themes = scandir($themesPath);
        foreach ($themes as $theme) {
            if ($theme === '.' || $theme === '..') {
                continue;
            }

            $themeAssetsPath = $themesPath . '/' . $theme . '/assets';
            if (is_dir($themeAssetsPath)) {
                $results[$theme] = self::publish($theme, $force);
            }
        }

        return $results;
    }

    /**
     * Clean published assets for a theme or admin
     *
     * @param string $theme Theme name or 'admin'
     * @return array Result with success status and message
     */
    public static function clean($theme) {
        // Determine path based on whether it's admin or theme assets
        if ($theme === 'admin') {
            $targetPath = root_path("public/assets/admin");
            $assetType = 'admin';
        } else {
            $targetPath = root_path("public/assets/themes/{$theme}");
            $assetType = "theme '{$theme}'";
        }

        if (!is_dir($targetPath)) {
            return [
                'success' => true,
                'message' => "No published assets found for {$assetType}",
            ];
        }

        try {
            self::deleteDirectory($targetPath);

            return [
                'success' => true,
                'message' => "Published assets for {$assetType} removed successfully",
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "Failed to remove assets: " . $e->getMessage(),
            ];
        }
    }

    /**
     * Recursively copy directory
     *
     * @param string $source Source directory
     * @param string $target Target directory
     * @throws \Exception
     */
    protected static function copyDirectory($source, $target) {
        if (!is_dir($target)) {
            if (!mkdir($target, 0755, true)) {
                throw new \Exception("Failed to create directory: {$target}");
            }
        }

        $dir = opendir($source);
        if ($dir === false) {
            throw new \Exception("Failed to open source directory: {$source}");
        }

        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $sourcePath = $source . '/' . $file;
            $targetPath = $target . '/' . $file;

            if (is_dir($sourcePath)) {
                self::copyDirectory($sourcePath, $targetPath);
            } else {
                if (!copy($sourcePath, $targetPath)) {
                    closedir($dir);
                    throw new \Exception("Failed to copy file: {$sourcePath} to {$targetPath}");
                }
            }
        }
        closedir($dir);
    }

    /**
     * Recursively delete directory
     *
     * @param string $dir Directory to delete
     * @throws \Exception
     */
    protected static function deleteDirectory($dir) {
        if (!is_dir($dir)) {
            return;
        }

        $items = scandir($dir);
        if ($items === false) {
            throw new \Exception("Failed to scan directory: {$dir}");
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                self::deleteDirectory($path);
            } else {
                if (!unlink($path)) {
                    throw new \Exception("Failed to delete file: {$path}");
                }
            }
        }

        if (!rmdir($dir)) {
            throw new \Exception("Failed to remove directory: {$dir}");
        }
    }
}
