<?php

namespace App\Core;

/**
 * Asset Publisher
 * Publishes theme assets to public folder
 */
class AssetPublisher {

    /**
     * Publish assets for a specific theme
     *
     * @param string $theme Theme name
     * @param bool $force Force republish even if assets exist
     * @return array Result with success status and message
     */
    public static function publish($theme, $force = false) {
        $sourcePath = base_path("themes/{$theme}/assets");
        $targetPath = base_path("public/themes/{$theme}/assets");

        // Check if source exists
        if (!is_dir($sourcePath)) {
            return [
                'success' => false,
                'message' => "Theme '{$theme}' assets not found at {$sourcePath}",
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
                'message' => "Assets for theme '{$theme}' published successfully",
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
     * Publish all theme assets
     *
     * @param bool $force Force republish even if assets exist
     * @return array Results for each theme
     */
    public static function publishAll($force = false) {
        $themesPath = base_path('themes');
        $results = [];

        if (!is_dir($themesPath)) {
            return [
                'success' => false,
                'message' => 'Themes directory not found',
            ];
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
     * Clean published assets for a theme
     *
     * @param string $theme Theme name
     * @return array Result with success status and message
     */
    public static function clean($theme) {
        $targetPath = base_path("public/themes/{$theme}/assets");

        if (!is_dir($targetPath)) {
            return [
                'success' => true,
                'message' => "No published assets found for theme '{$theme}'",
            ];
        }

        try {
            self::deleteDirectory($targetPath);

            return [
                'success' => true,
                'message' => "Published assets for theme '{$theme}' removed successfully",
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
