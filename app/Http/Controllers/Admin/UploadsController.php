<?php

namespace App\Http\Controllers\Admin;

class UploadsController {

    private $uploadDir = 'public/uploads';
    private $maxFileSize = 209715200; // 200MB
    private $allowedExtensions = [
        // Images
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico',
        // Documents
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'csv',
        // Videos
        'mp4', 'webm', 'avi', 'mov',
        // Audio
        'mp3', 'wav', 'ogg', 'm4a'
    ];

    /**
     * Display uploads manager
     */
    public function index() {
        $type = request()->get('type', 'all');
        $search = request()->get('search', '');
        $path = request()->get('path', '');

        $basePath = base_path($this->uploadDir);
        $currentPath = $path ? $basePath . '/' . $path : $basePath;

        // Ensure upload directory exists
        if (!is_dir($basePath)) {
            mkdir($basePath, 0755, true);
        }

        // Get files and directories
        $items = $this->scanDirectory($currentPath, $type, $search);

        // Calculate stats
        $stats = $this->calculateStats($basePath);

        // Build breadcrumbs
        $breadcrumbs = $this->buildBreadcrumbs($path);

        return admin_view('uploads.index', [
            'title' => 'Uploads Manager',
            'items' => $items,
            'stats' => $stats,
            'current_path' => $path,
            'breadcrumbs' => $breadcrumbs,
            'type_filter' => $type,
            'search' => $search
        ]);
    }

    /**
     * Handle file upload
     */
    public function store() {
        // Validate file upload
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'No file uploaded or upload error occurred';
            redirect(url('/admin/uploads'));
            return;
        }

        $file = $_FILES['file'];
        $targetPath = request()->post('path', '');

        // Validate file size
        if ($file['size'] > $this->maxFileSize) {
            $_SESSION['error'] = 'File size exceeds maximum allowed size (200MB)';
            redirect(url('/admin/uploads'));
            return;
        }

        // Validate file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedExtensions)) {
            $_SESSION['error'] = 'File type not allowed';
            redirect(url('/admin/uploads'));
            return;
        }

        // Determine upload path
        $year = date('Y');
        $month = date('m');
        $uploadPath = $targetPath ? "{$this->uploadDir}/{$targetPath}" : "{$this->uploadDir}/{$year}/{$month}";
        $fullUploadPath = base_path($uploadPath);

        if (!is_dir($fullUploadPath)) {
            mkdir($fullUploadPath, 0755, true);
        }

        // Generate unique filename if file exists
        $filename = $file['name'];
        $counter = 1;
        while (file_exists($fullUploadPath . '/' . $filename)) {
            $name = pathinfo($file['name'], PATHINFO_FILENAME);
            $filename = $name . '_' . $counter . '.' . $extension;
            $counter++;
        }

        $fullFilePath = $fullUploadPath . '/' . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $fullFilePath)) {
            $_SESSION['error'] = 'Failed to save uploaded file';
            redirect(url('/admin/uploads'));
            return;
        }

        // Create thumbnail for images
        if ($this->isImage($filename)) {
            $this->createThumbnail($fullFilePath, $fullUploadPath);
        }

        $_SESSION['success'] = 'File uploaded successfully';
        redirect(url('/admin/uploads' . ($targetPath ? '?path=' . urlencode($targetPath) : '')));
    }

    /**
     * Create new folder
     */
    public function createFolder() {
        $folderName = request()->post('folder_name');
        $currentPath = request()->post('current_path', '');

        if (!$folderName) {
            $_SESSION['error'] = 'Folder name is required';
            redirect(url('/admin/uploads' . ($currentPath ? '?path=' . urlencode($currentPath) : '')));
            return;
        }

        // Sanitize folder name
        $folderName = preg_replace('/[^a-zA-Z0-9_-]/', '', $folderName);

        $basePath = base_path($this->uploadDir);
        $targetPath = $currentPath ? $basePath . '/' . $currentPath . '/' . $folderName : $basePath . '/' . $folderName;

        if (file_exists($targetPath)) {
            $_SESSION['error'] = 'Folder already exists';
        } elseif (mkdir($targetPath, 0755, true)) {
            $_SESSION['success'] = 'Folder created successfully';
        } else {
            $_SESSION['error'] = 'Failed to create folder';
        }

        redirect(url('/admin/uploads' . ($currentPath ? '?path=' . urlencode($currentPath) : '')));
    }

    /**
     * Delete file or folder
     */
    public function delete() {
        $item = request()->post('item');
        $currentPath = request()->post('current_path', '');

        if (!$item) {
            $_SESSION['error'] = 'No item specified';
            redirect(url('/admin/uploads' . ($currentPath ? '?path=' . urlencode($currentPath) : '')));
            return;
        }

        $basePath = base_path($this->uploadDir);
        $itemPath = $currentPath ? $basePath . '/' . $currentPath . '/' . $item : $basePath . '/' . $item;

        if (!file_exists($itemPath)) {
            $_SESSION['error'] = 'Item not found';
        } elseif (is_dir($itemPath)) {
            if ($this->deleteDirectory($itemPath)) {
                $_SESSION['success'] = 'Folder deleted successfully';
            } else {
                $_SESSION['error'] = 'Failed to delete folder';
            }
        } else {
            // Delete thumbnail if exists
            if ($this->isImage($item)) {
                $dir = dirname($itemPath);
                $thumbPath = $dir . '/thumbnails/' . basename($itemPath);
                if (file_exists($thumbPath)) {
                    unlink($thumbPath);
                }
            }

            if (unlink($itemPath)) {
                $_SESSION['success'] = 'File deleted successfully';
            } else {
                $_SESSION['error'] = 'Failed to delete file';
            }
        }

        redirect(url('/admin/uploads' . ($currentPath ? '?path=' . urlencode($currentPath) : '')));
    }

    /**
     * Get file info (AJAX)
     */
    public function fileInfo() {
        $file = request()->get('file');
        $path = request()->get('path', '');

        if (!$file) {
            json(['error' => 'No file specified'], 400);
            return;
        }

        $basePath = base_path($this->uploadDir);
        $filePath = $path ? $basePath . '/' . $path . '/' . $file : $basePath . '/' . $file;

        if (!file_exists($filePath) || is_dir($filePath)) {
            json(['error' => 'File not found'], 404);
            return;
        }

        $info = [
            'name' => $file,
            'size' => filesize($filePath),
            'formatted_size' => $this->formatFileSize(filesize($filePath)),
            'type' => $this->getFileType($file),
            'mime' => mime_content_type($filePath),
            'modified' => date('Y-m-d H:i:s', filemtime($filePath)),
            'url' => url($this->uploadDir . '/' . ($path ? $path . '/' : '') . $file)
        ];

        if ($this->isImage($file)) {
            list($width, $height) = getimagesize($filePath);
            $info['dimensions'] = $width . ' × ' . $height;
        }

        json($info);
    }

    /**
     * Scan directory for files and folders
     */
    private function scanDirectory($path, $typeFilter = 'all', $search = '') {
        if (!is_dir($path)) {
            return [];
        }

        $items = [];
        $files = scandir($path);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === 'thumbnails') {
                continue;
            }

            $fullPath = $path . '/' . $file;
            $isDir = is_dir($fullPath);

            // Apply search filter
            if ($search && stripos($file, $search) === false) {
                continue;
            }

            // Apply type filter
            if (!$isDir && $typeFilter !== 'all') {
                $fileType = $this->getFileType($file);
                if ($typeFilter !== $fileType) {
                    continue;
                }
            }

            $item = [
                'name' => $file,
                'is_dir' => $isDir,
                'size' => $isDir ? null : filesize($fullPath),
                'formatted_size' => $isDir ? '-' : $this->formatFileSize(filesize($fullPath)),
                'type' => $isDir ? 'folder' : $this->getFileType($file),
                'modified' => date('Y-m-d H:i:s', filemtime($fullPath))
            ];

            if (!$isDir) {
                $relativePath = str_replace(base_path() . '/', '', $fullPath);
                $item['url'] = url($relativePath);

                // Add thumbnail URL for images
                if ($this->isImage($file)) {
                    $thumbPath = dirname($fullPath) . '/thumbnails/' . $file;
                    if (file_exists($thumbPath)) {
                        $thumbRelativePath = str_replace(base_path() . '/', '', $thumbPath);
                        $item['thumbnail'] = url($thumbRelativePath);
                    } else {
                        $item['thumbnail'] = $item['url'];
                    }
                }
            }

            $items[] = $item;
        }

        // Sort: folders first, then by name
        usort($items, function($a, $b) {
            if ($a['is_dir'] !== $b['is_dir']) {
                return $b['is_dir'] - $a['is_dir'];
            }
            return strcasecmp($a['name'], $b['name']);
        });

        return $items;
    }

    /**
     * Calculate storage statistics
     */
    private function calculateStats($path) {
        $stats = [
            'total_files' => 0,
            'total_size' => 0,
            'total_folders' => 0,
            'images' => 0,
            'documents' => 0,
            'videos' => 0,
            'audio' => 0,
            'other' => 0
        ];

        $this->scanDirectoryRecursive($path, $stats);

        $stats['formatted_size'] = $this->formatFileSize($stats['total_size']);
        return $stats;
    }

    /**
     * Recursively scan directory for stats
     */
    private function scanDirectoryRecursive($path, &$stats) {
        if (!is_dir($path)) {
            return;
        }

        $files = scandir($path);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === 'thumbnails') {
                continue;
            }

            $fullPath = $path . '/' . $file;
            if (is_dir($fullPath)) {
                $stats['total_folders']++;
                $this->scanDirectoryRecursive($fullPath, $stats);
            } else {
                $stats['total_files']++;
                $stats['total_size'] += filesize($fullPath);

                $type = $this->getFileType($file);
                if (isset($stats[$type . 's'])) {
                    $stats[$type . 's']++;
                } else {
                    $stats['other']++;
                }
            }
        }
    }

    /**
     * Build breadcrumbs from path
     */
    private function buildBreadcrumbs($path) {
        $breadcrumbs = [
            ['name' => 'Uploads', 'path' => '']
        ];

        if (!$path) {
            return $breadcrumbs;
        }

        $parts = explode('/', $path);
        $currentPath = '';

        foreach ($parts as $part) {
            if ($part) {
                $currentPath .= ($currentPath ? '/' : '') . $part;
                $breadcrumbs[] = [
                    'name' => $part,
                    'path' => $currentPath
                ];
            }
        }

        return $breadcrumbs;
    }

    /**
     * Delete directory recursively
     */
    private function deleteDirectory($dir) {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        return rmdir($dir);
    }

    /**
     * Check if file is an image
     */
    private function isImage($filename) {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, $imageExtensions);
    }

    /**
     * Get file type from filename
     */
    private function getFileType($filename) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $types = [
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico'],
            'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'csv'],
            'video' => ['mp4', 'webm', 'avi', 'mov'],
            'audio' => ['mp3', 'wav', 'ogg']
        ];

        foreach ($types as $type => $extensions) {
            if (in_array($ext, $extensions)) {
                return $type;
            }
        }

        return 'other';
    }

    /**
     * Format file size
     */
    private function formatFileSize($bytes) {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Create thumbnail for image
     */
    private function createThumbnail($sourcePath, $uploadDir) {
        $thumbDir = $uploadDir . '/thumbnails';
        if (!is_dir($thumbDir)) {
            mkdir($thumbDir, 0755, true);
        }

        $filename = basename($sourcePath);
        $thumbPath = $thumbDir . '/' . $filename;

        // Skip if thumbnail already exists
        if (file_exists($thumbPath)) {
            return true;
        }

        // Get image info
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            return false;
        }

        list($width, $height, $type) = $imageInfo;

        // Skip if image is already small
        if ($width <= 300 && $height <= 300) {
            return false;
        }

        // Set thumbnail dimensions
        $thumbWidth = 300;
        $thumbHeight = 300;

        // Calculate aspect ratio
        $aspectRatio = $width / $height;
        if ($width > $height) {
            $thumbHeight = $thumbWidth / $aspectRatio;
        } else {
            $thumbWidth = $thumbHeight * $aspectRatio;
        }

        // Create image resource
        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($sourcePath);
                break;
            case IMAGETYPE_WEBP:
                $source = imagecreatefromwebp($sourcePath);
                break;
            default:
                return false;
        }

        if (!$source) {
            return false;
        }

        // Create thumbnail
        $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);

        // Preserve transparency for PNG and GIF
        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
            imagecolortransparent($thumb, imagecolorallocatealpha($thumb, 0, 0, 0, 127));
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
        }

        // Copy and resize
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);

        // Save thumbnail
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($thumb, $thumbPath, 85);
                break;
            case IMAGETYPE_PNG:
                imagepng($thumb, $thumbPath, 8);
                break;
            case IMAGETYPE_GIF:
                imagegif($thumb, $thumbPath);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($thumb, $thumbPath, 85);
                break;
        }

        // Clean up
        imagedestroy($source);
        imagedestroy($thumb);

        return true;
    }
}