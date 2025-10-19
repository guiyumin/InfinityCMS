<div class="admin-container">
    <div class="page-header">
        <h1>Uploads Manager</h1>
        <div class="page-actions">
            <button onclick="showUploadModal()" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                Upload Files
            </button>
            <button onclick="showCreateFolderModal()" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                    <line x1="12" y1="11" x2="12" y2="17"></line>
                    <line x1="9" y1="14" x2="15" y2="14"></line>
                </svg>
                New Folder
            </button>
        </div>
    </div>

    <!-- Storage Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= $stats['total_files'] ?></div>
                <div class="stat-label">Total Files</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                </svg>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= $stats['total_folders'] ?></div>
                <div class="stat-label">Folders</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="10" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= $stats['formatted_size'] ?></div>
                <div class="stat-label">Total Size</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                    <polyline points="21 15 16 10 5 21"></polyline>
                </svg>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= $stats['images'] ?></div>
                <div class="stat-label">Images</div>
            </div>
        </div>
    </div>

    <!-- Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Toolbar -->
    <div class="toolbar">
        <div class="breadcrumbs">
            <?php foreach ($breadcrumbs as $index => $crumb): ?>
                <?php if ($index < count($breadcrumbs) - 1): ?>
                    <a href="<?= url('/admin/uploads' . ($crumb['path'] ? '?path=' . urlencode($crumb['path']) : '')) ?>" class="breadcrumb-link">
                        <?= htmlspecialchars($crumb['name']) ?>
                    </a>
                    <span class="breadcrumb-separator">/</span>
                <?php else: ?>
                    <span class="breadcrumb-current"><?= htmlspecialchars($crumb['name']) ?></span>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div class="filters">
            <form method="GET" action="<?= url('/admin/uploads') ?>" class="filter-form">
                <?php if ($current_path): ?>
                    <input type="hidden" name="path" value="<?= htmlspecialchars($current_path) ?>">
                <?php endif; ?>

                <select name="type" class="filter-select" onchange="this.form.submit()">
                    <option value="all" <?= $type_filter === 'all' ? 'selected' : '' ?>>All Files</option>
                    <option value="image" <?= $type_filter === 'image' ? 'selected' : '' ?>>Images</option>
                    <option value="document" <?= $type_filter === 'document' ? 'selected' : '' ?>>Documents</option>
                    <option value="video" <?= $type_filter === 'video' ? 'selected' : '' ?>>Videos</option>
                    <option value="audio" <?= $type_filter === 'audio' ? 'selected' : '' ?>>Audio</option>
                    <option value="other" <?= $type_filter === 'other' ? 'selected' : '' ?>>Other</option>
                </select>

                <input type="text" name="search" class="filter-search" placeholder="Search files..." value="<?= htmlspecialchars($search) ?>">

                <button type="submit" class="btn btn-sm">Search</button>
            </form>
        </div>
    </div>

    <!-- Files Grid -->
    <div class="files-grid">
        <?php if (empty($items)): ?>
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                    <polyline points="13 2 13 9 20 9"></polyline>
                </svg>
                <p>No files found</p>
            </div>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <div class="file-item <?= $item['is_dir'] ? 'folder' : 'file' ?>" data-name="<?= htmlspecialchars($item['name']) ?>">
                    <?php if ($item['is_dir']): ?>
                        <a href="<?= url('/admin/uploads?path=' . urlencode(($current_path ? $current_path . '/' : '') . $item['name'])) ?>" class="file-link">
                            <div class="file-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                                </svg>
                            </div>
                            <div class="file-name"><?= htmlspecialchars($item['name']) ?></div>
                            <div class="file-info">Folder</div>
                        </a>
                    <?php else: ?>
                        <div class="file-link" onclick="showFileInfo('<?= htmlspecialchars($item['name']) ?>')">
                            <div class="file-icon">
                                <?php if ($item['type'] === 'image' && isset($item['thumbnail'])): ?>
                                    <img src="<?= $item['thumbnail'] ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="file-thumbnail">
                                <?php else: ?>
                                    <?= getFileIcon($item['type']) ?>
                                <?php endif; ?>
                            </div>
                            <div class="file-name"><?= htmlspecialchars($item['name']) ?></div>
                            <div class="file-info"><?= $item['formatted_size'] ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="file-actions">
                        <?php if (!$item['is_dir']): ?>
                            <button onclick="copyUrl('<?= $item['url'] ?>')" class="btn-icon" title="Copy URL">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                                </svg>
                            </button>
                        <?php endif; ?>
                        <button onclick="deleteItem('<?= htmlspecialchars($item['name']) ?>', <?= $item['is_dir'] ? 'true' : 'false' ?>)" class="btn-icon btn-danger" title="Delete">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Upload Files</h2>
            <button onclick="closeUploadModal()" class="modal-close">&times;</button>
        </div>
        <form method="POST" action="<?= url('/admin/uploads/store') ?>" enctype="multipart/form-data">
            <input type="hidden" name="path" value="<?= htmlspecialchars($current_path) ?>">

            <div class="upload-area" id="uploadArea">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <p>Drop files here or click to browse</p>
                <small>Maximum file size: 200MB</small>
                <input type="file" name="file" id="fileInput" required>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Upload</button>
                <button type="button" onclick="closeUploadModal()" class="btn btn-secondary">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Create Folder Modal -->
<div id="createFolderModal" class="modal">
    <div class="modal-content modal-small">
        <div class="modal-header">
            <h2>Create New Folder</h2>
            <button onclick="closeCreateFolderModal()" class="modal-close">&times;</button>
        </div>
        <form method="POST" action="<?= url('/admin/uploads/create-folder') ?>">
            <input type="hidden" name="current_path" value="<?= htmlspecialchars($current_path) ?>">

            <div class="form-group">
                <label for="folder_name">Folder Name</label>
                <input type="text" name="folder_name" id="folder_name" class="form-control" pattern="[a-zA-Z0-9_-]+" required>
                <small class="form-help">Only letters, numbers, hyphens, and underscores allowed</small>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Create Folder</button>
                <button type="button" onclick="closeCreateFolderModal()" class="btn btn-secondary">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- File Info Modal -->
<div id="fileInfoModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>File Information</h2>
            <button onclick="closeFileInfoModal()" class="modal-close">&times;</button>
        </div>
        <div id="fileInfoContent">
            <!-- File info will be loaded here -->
        </div>
    </div>
</div>

<?php
function getFileIcon($type) {
    $icons = [
        'document' => '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>',
        'video' => '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"></rect><line x1="7" y1="2" x2="7" y2="22"></line><line x1="17" y1="2" x2="17" y2="22"></line><line x1="2" y1="12" x2="22" y2="12"></line><line x1="2" y1="7" x2="7" y2="7"></line><line x1="2" y1="17" x2="7" y2="17"></line><line x1="17" y1="17" x2="22" y2="17"></line><line x1="17" y1="7" x2="22" y2="7"></line></svg>',
        'audio' => '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>',
        'other' => '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>'
    ];

    return $icons[$type] ?? $icons['other'];
}
?>

<style>
.admin-container {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.page-header h1 {
    font-size: 1.75rem;
    font-weight: 600;
    color: #1e293b;
}

.page-actions {
    display: flex;
    gap: 0.5rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 0.5rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    color: #3b82f6;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1e293b;
}

.stat-label {
    font-size: 0.875rem;
    color: #64748b;
}

.alert {
    padding: 1rem;
    border-radius: 0.375rem;
    margin-bottom: 1rem;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.toolbar {
    background: white;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.breadcrumbs {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.breadcrumb-link {
    color: #3b82f6;
    text-decoration: none;
}

.breadcrumb-link:hover {
    text-decoration: underline;
}

.breadcrumb-separator {
    color: #94a3b8;
}

.breadcrumb-current {
    color: #1e293b;
    font-weight: 500;
}

.filters {
    display: flex;
    gap: 0.5rem;
}

.filter-form {
    display: flex;
    gap: 0.5rem;
}

.filter-select, .filter-search {
    padding: 0.375rem 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 0.875rem;
}

.filter-search {
    width: 200px;
}

.files-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
    color: #94a3b8;
}

.file-item {
    background: white;
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s, box-shadow 0.2s;
    position: relative;
}

.file-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.file-link {
    display: block;
    padding: 1rem;
    text-align: center;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
}

.file-icon {
    margin-bottom: 0.5rem;
    color: #64748b;
}

.file-thumbnail {
    width: 100%;
    height: 100px;
    object-fit: cover;
    border-radius: 0.25rem;
}

.file-name {
    font-size: 0.875rem;
    color: #1e293b;
    word-break: break-all;
    margin-bottom: 0.25rem;
}

.file-info {
    font-size: 0.75rem;
    color: #94a3b8;
}

.file-actions {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    display: flex;
    gap: 0.25rem;
    opacity: 0;
    transition: opacity 0.2s;
}

.file-item:hover .file-actions {
    opacity: 1;
}

.btn-icon {
    padding: 0.25rem;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.25rem;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-icon:hover {
    background: #f3f4f6;
}

.btn-icon.btn-danger:hover {
    background: #fee2e2;
    border-color: #fecaca;
    color: #dc2626;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.modal.show {
    display: flex;
}

.modal-content {
    background: white;
    border-radius: 0.5rem;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-small {
    max-width: 400px;
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h2 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1e293b;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #64748b;
    cursor: pointer;
}

.modal-footer {
    padding: 1.5rem;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
}

.upload-area {
    margin: 2rem;
    padding: 3rem;
    border: 2px dashed #cbd5e1;
    border-radius: 0.5rem;
    text-align: center;
    color: #64748b;
    position: relative;
    cursor: pointer;
    transition: all 0.2s;
}

.upload-area:hover {
    border-color: #3b82f6;
    background: #f0f9ff;
}

.upload-area input[type="file"] {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.form-group {
    padding: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
}

.form-control {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 0.875rem;
}

.form-help {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.75rem;
    color: #6b7280;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-sm {
    padding: 0.25rem 0.75rem;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn-secondary {
    background: #64748b;
    color: white;
}

.btn-secondary:hover {
    background: #475569;
}
</style>

<script>
function showUploadModal() {
    document.getElementById('uploadModal').classList.add('show');
}

function closeUploadModal() {
    document.getElementById('uploadModal').classList.remove('show');
}

function showCreateFolderModal() {
    document.getElementById('createFolderModal').classList.add('show');
}

function closeCreateFolderModal() {
    document.getElementById('createFolderModal').classList.remove('show');
}

function showFileInfo(filename) {
    const currentPath = '<?= $current_path ?>';
    const url = '<?= url('/admin/uploads/file-info') ?>?file=' + encodeURIComponent(filename) + '&path=' + encodeURIComponent(currentPath);

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            let html = '<div style="padding: 1.5rem;">';
            html += '<p><strong>Filename:</strong> ' + data.name + '</p>';
            html += '<p><strong>Size:</strong> ' + data.formatted_size + '</p>';
            html += '<p><strong>Type:</strong> ' + data.type + '</p>';
            html += '<p><strong>MIME Type:</strong> ' + data.mime + '</p>';
            html += '<p><strong>Modified:</strong> ' + data.modified + '</p>';

            if (data.dimensions) {
                html += '<p><strong>Dimensions:</strong> ' + data.dimensions + ' pixels</p>';
            }

            html += '<p><strong>URL:</strong></p>';
            html += '<input type="text" value="' + data.url + '" class="form-control" readonly>';
            html += '<button onclick="copyUrl(\'' + data.url + '\')" class="btn btn-primary" style="margin-top: 0.5rem;">Copy URL</button>';

            if (data.type === 'image') {
                html += '<div style="margin-top: 1rem;"><img src="' + data.url + '" style="max-width: 100%; border-radius: 0.375rem;"></div>';
            }

            html += '</div>';

            document.getElementById('fileInfoContent').innerHTML = html;
            document.getElementById('fileInfoModal').classList.add('show');
        })
        .catch(error => {
            alert('Failed to load file information');
        });
}

function closeFileInfoModal() {
    document.getElementById('fileInfoModal').classList.remove('show');
}

function copyUrl(url) {
    navigator.clipboard.writeText(url).then(() => {
        alert('URL copied to clipboard!');
    });
}

function deleteItem(name, isFolder) {
    const type = isFolder ? 'folder' : 'file';
    if (!confirm('Are you sure you want to delete this ' + type + '?')) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= url('/admin/uploads/delete') ?>';

    const itemInput = document.createElement('input');
    itemInput.type = 'hidden';
    itemInput.name = 'item';
    itemInput.value = name;

    const pathInput = document.createElement('input');
    pathInput.type = 'hidden';
    pathInput.name = 'current_path';
    pathInput.value = '<?= $current_path ?>';

    form.appendChild(itemInput);
    form.appendChild(pathInput);
    document.body.appendChild(form);
    form.submit();
}

// Drag and drop support
const uploadArea = document.getElementById('uploadArea');
const fileInput = document.getElementById('fileInput');

if (uploadArea) {
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = '#3b82f6';
        uploadArea.style.background = '#f0f9ff';
    });

    uploadArea.addEventListener('dragleave', (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = '#cbd5e1';
        uploadArea.style.background = '';
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = '#cbd5e1';
        uploadArea.style.background = '';

        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
        }
    });
}
</script>