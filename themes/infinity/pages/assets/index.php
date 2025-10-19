<div class="admin-assets" x-data="{ showConfirm: false, action: '', selectedTheme: '' }">
    <div class="page-header">
        <h1>Asset Management</h1>
        <p>Publish and manage theme assets for web access</p>
    </div>

    <!-- Flash Messages -->
    <?php if ($success): ?>
        <div class="alert alert-success">
            <strong>Success!</strong> <?= e($success) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <strong>Error!</strong> <?= e($error) ?>
        </div>
    <?php endif; ?>

    <!-- Quick Actions -->
    <div class="asset-actions">
        <form method="POST" action="<?= url('/admin/assets/publish-all') ?>" style="display: inline;">
            <button type="submit" class="btn btn-primary">
                Publish All Themes
            </button>
        </form>

        <form method="POST" action="<?= url('/admin/assets/publish-all') ?>" style="display: inline;">
            <input type="hidden" name="force" value="true">
            <button type="submit" class="btn btn-warning">
                Force Republish All
            </button>
        </form>
    </div>

    <!-- Info Box -->
    <div class="info-box">
        <h3>What are Assets?</h3>
        <p>
            Theme assets (CSS, JavaScript, images, etc.) need to be published from <code>themes/[theme]/assets/</code>
            to <code>public/assets/themes/[theme]/</code> to be accessible via the web.
        </p>
        <p>
            This is necessary because most web hosts don't allow direct access to files outside the public folder.
            Use this page to publish assets whenever you update your theme files.
        </p>
    </div>

    <!-- Theme Assets Table -->
    <div class="theme-assets-container">
        <h2>Available Themes</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Theme Name</th>
                    <th>Status</th>
                    <th>Assets Exist</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($themes)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 2rem;">
                            No themes found in the themes directory.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($themes as $theme): ?>
                        <tr class="<?= $theme['is_current'] ? 'current-theme' : '' ?>">
                            <td>
                                <strong><?= e($theme['name']) ?></strong>
                                <?php if ($theme['is_current']): ?>
                                    <span class="badge badge-info">Current</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!$theme['has_assets']): ?>
                                    <span class="badge badge-secondary">No Assets</span>
                                <?php elseif ($theme['is_published']): ?>
                                    <span class="badge badge-success">Published</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Not Published</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $theme['has_assets'] ? '✓ Yes' : '✗ No' ?>
                            </td>
                            <td>
                                <?php if ($theme['has_assets']): ?>
                                    <div class="action-buttons">
                                        <!-- Publish Button -->
                                        <form method="POST" action="<?= url('/admin/assets/publish') ?>" style="display: inline;">
                                            <input type="hidden" name="theme" value="<?= e($theme['name']) ?>">
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <?= $theme['is_published'] ? 'Republish' : 'Publish' ?>
                                            </button>
                                        </form>

                                        <!-- Force Republish Button -->
                                        <?php if ($theme['is_published']): ?>
                                            <form method="POST" action="<?= url('/admin/assets/publish') ?>" style="display: inline;">
                                                <input type="hidden" name="theme" value="<?= e($theme['name']) ?>">
                                                <input type="hidden" name="force" value="true">
                                                <button type="submit" class="btn btn-sm btn-warning">
                                                    Force
                                                </button>
                                            </form>

                                            <!-- Clean Button -->
                                            <button
                                                class="btn btn-sm btn-danger"
                                                @click="showConfirm = true; action = 'clean'; selectedTheme = '<?= e($theme['name']) ?>'">
                                                Clean
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Help Section -->
    <div class="help-section">
        <h3>Understanding Asset Publishing</h3>
        <ul>
            <li><strong>Publish:</strong> Copies assets to the public folder (skips if already exists)</li>
            <li><strong>Republish:</strong> Same as Publish, useful if you've updated theme files</li>
            <li><strong>Force:</strong> Deletes and re-copies all assets (use after major updates)</li>
            <li><strong>Clean:</strong> Removes published assets from public folder</li>
        </ul>
        <p class="text-muted">
            <strong>Note:</strong> Always publish assets after uploading new theme files or making changes to CSS/JS/images.
        </p>
    </div>

    <!-- Confirmation Modal -->
    <div x-show="showConfirm"
         x-cloak
         class="modal-overlay"
         @click.self="showConfirm = false">
        <div class="modal">
            <h3>Confirm Action</h3>
            <p x-show="action === 'clean'">
                Are you sure you want to clean published assets for <strong x-text="selectedTheme"></strong>?<br>
                This will remove all published assets for this theme.
            </p>

            <div class="modal-actions">
                <button
                    class="btn btn-secondary"
                    @click="showConfirm = false">
                    Cancel
                </button>
                <form method="POST" action="<?= url('/admin/assets/clean') ?>" style="display: inline;">
                    <input type="hidden" name="theme" x-bind:value="selectedTheme">
                    <button
                        type="submit"
                        class="btn btn-danger"
                        @click="showConfirm = false">
                        Confirm
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.admin-assets {
    padding: 2rem;
}

.page-header {
    margin-bottom: 2rem;
}

.page-header h1 {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.page-header p {
    color: #6c757d;
}

.asset-actions {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
}

.alert {
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1rem;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.info-box {
    background: #e7f3ff;
    border-left: 4px solid #0066cc;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border-radius: 4px;
}

.info-box h3 {
    margin-top: 0;
    margin-bottom: 0.5rem;
    color: #0066cc;
}

.info-box p {
    margin: 0.5rem 0;
}

.info-box code {
    background: rgba(0, 0, 0, 0.1);
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.9em;
}

.theme-assets-container {
    margin-bottom: 2rem;
}

.theme-assets-container h2 {
    margin-bottom: 1rem;
}

.table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.table th,
.table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #dee2e6;
}

.table thead th {
    background: #f8f9fa;
    font-weight: 600;
}

.table tbody tr.current-theme {
    background: #f0f8ff;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-size: 0.875rem;
    margin-left: 0.5rem;
}

.badge-success {
    background: #28a745;
    color: white;
}

.badge-warning {
    background: #ffc107;
    color: #000;
}

.badge-info {
    background: #17a2b8;
    color: white;
}

.badge-secondary {
    background: #6c757d;
    color: white;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    color: white;
}

.btn-sm {
    padding: 0.25rem 0.75rem;
    font-size: 0.875rem;
}

.btn-primary {
    background: #007bff;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-warning {
    background: #ffc107;
    color: #000;
}

.btn-warning:hover {
    background: #e0a800;
}

.btn-danger {
    background: #dc3545;
}

.btn-danger:hover {
    background: #c82333;
}

.btn-secondary {
    background: #6c757d;
}

.btn-secondary:hover {
    background: #5a6268;
}

.text-muted {
    color: #6c757d;
}

.help-section {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 4px;
    margin-top: 2rem;
}

.help-section h3 {
    margin-top: 0;
}

.help-section ul {
    margin: 1rem 0;
}

.help-section li {
    margin: 0.5rem 0;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    max-width: 500px;
    width: 90%;
}

.modal h3 {
    margin-bottom: 1rem;
}

.modal-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    justify-content: flex-end;
}

[x-cloak] {
    display: none !important;
}
</style>
